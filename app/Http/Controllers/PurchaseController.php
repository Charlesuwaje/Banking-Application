<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Bid;
use App\Models\Wallet;
use App\Models\GiftCard;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Unicodeveloper\Paystack\Facades\Paystack;
// use Paystack;

class PurchaseController extends Controller
{

    public function showPurchaseForm(GiftCard $giftcard)
    {
        $highestBid = $giftcard->bids()->orderByDesc('amount')->first();

        if (!$highestBid) {
            return redirect()->back()->withErrors(['error' => 'No bids have been placed for this gift card.']);
        }

        // if ($giftcard->status !== 'ended') {
        //     return redirect()->back()->withErrors(['error' => 'The auction for this gift card has not ended.']);
        // }

        if ($highestBid->user_id !== Auth::id()) {
            return redirect()->back()->withErrors(['error' => 'You are not the highest bidder.']);
        }

        return view('purchases.create', [
            'giftcard' => $giftcard,
            'highestBid' => $highestBid,
        ]);
    }



    public function store(Request $request, GiftCard $giftcard)
    {
        $request->validate([
            'payment_method' => 'required|in:transfer,paystack',
        ]);

        $highestBid = $giftcard->bids()->orderByDesc('amount')->first();

        if (!$highestBid) {
            return back()->withErrors(['error' => 'No bids have been placed for this gift card.']);
        }

        // if ($giftcard->status !== 'ended') {
        //     return back()->withErrors(['error' => 'The auction for this gift card has not ended.']);
        // }

        if ($highestBid->user_id !== Auth::id()) {
            return back()->withErrors(['error' => 'You are not the highest bidder.']);
        }

        try {
            DB::beginTransaction();

            Purchase::create([
                'gift_card_id' => $giftcard->id,
                'buyer_id' => Auth::id(),
                'amount' => $highestBid->amount,
                'payment_method' => $request->payment_method,
                'status' => 'pending',
            ]);

            DB::commit();

            return $request->payment_method === 'paystack'
                ? redirect()->route('purchase.pay', $giftcard->id)
                : redirect()->route('purchase.transfer', $giftcard->id)
                ->with('success', 'Please proceed with the bank transfer.');
            //         return $request->payment_method === 'paystack'
            // ? redirect()->route('purchase.pay', $giftcard->id)
            // : redirect()->route('purchase.transfer', $giftcard->id)
            // ->with('success', 'Please proceed with the bank transfer.');

        } catch (Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'An error occurred while processing your purchase.']);
        }
    }


    // public function pay(GiftCard $giftcard)
    // {
    //     $purchase = Purchase::where('gift_card_id', $giftcard->id)
    //         ->where('buyer_id', Auth::id())
    //         ->where('status', 'pending')
    //         ->firstOrFail();

    //     $data = [
    //         'email' => Auth::user()->email,
    //         'amount' => $purchase->amount * 100, // Amount in kobo
    //         'reference' => 'GC-' . $purchase->id . '-' . uniqid(),
    //         'callback_url' => route('purchase.callback'), // Callback for Paystack response
    //         'metadata' => [
    //             'gift_card_id' => $giftcard->id,
    //             'purchase_id' => $purchase->id,
    //         ],
    //     ];

    //     try {
    //         $payment = Paystack::getAuthorizationUrl($data);
    //         return redirect($payment->authorization_url); // Access authorization_url property
    //     } catch (\Exception $e) {
    //         return back()->withErrors(['error' => 'Failed to initialize Paystack payment: ' . $e->getMessage()]);
    //     }
    // }

    public function pay(GiftCard $giftcard)
    {
        $purchase = Purchase::where('gift_card_id', $giftcard->id)
            ->where('buyer_id', Auth::id())
            ->where('status', 'pending')
            ->firstOrFail();

        $data = [
            'email' => Auth::user()->email,
            'amount' => $purchase->amount * 100,
            'reference' => 'GC-' . $purchase->id . '-' . uniqid(),
            'callback_url' => route('purchase.callback'), // Callback URL
            'metadata' => [
                'gift_card_id' => $giftcard->id,
                'purchase_id' => $purchase->id,
            ],
        ];

        try {
            // Pass the data directly to getAuthorizationUrl
            $payment = Paystack::getAuthorizationUrl($data);

            // Redirect to Paystack gateway
            return redirect($payment->url);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to initialize Paystack payment: ' . $e->getMessage()]);
        }
    }



    // public function handlePaystackCallback(Request $request)
    // {
    //     $paymentDetails = Paystack::getPaymentData();

    //     if ($paymentDetails['status'] === true) {
    //         $reference = $paymentDetails['data']['reference'];
    //         $purchaseId = $paymentDetails['data']['metadata']['purchase_id'];
    //         $purchase = Purchase::findOrFail($purchaseId);

    //         if ($purchase->status === 'pending') {
    //             $wallet = Wallet::where('user_id', Auth::id())->firstOrFail();

    //             if ($wallet->balance >= $purchase->amount) {
    //                 $wallet->balance -= $purchase->amount;
    //                 $wallet->save();

    //                 $purchase->status = 'completed';
    //                 $purchase->save();

    //                 // return redirect()->route('giftcards.my-bids')->with('success', 'Payment successful, wallet balance updated!');
    //                 return redirect()->route('giftcards.index')->with('success', 'Purchase successful Payment successful, wallet balance updated!');
    //             } else {
    //                 return redirect()->route('giftcards.my-bids')->withErrors(['error' => 'Insufficient wallet balance!']);
    //             }
    //         }

    //         return redirect()->route('giftcards.my-bids')->with('info', 'Purchase has already been processed.');
    //     }

    //     return redirect()->route('giftcards.my-bids')->withErrors(['error' => 'Payment verification failed.']);
    // }

    public function handlePaystackCallback(Request $request)
    {
        $paymentDetails = Paystack::getPaymentData();

        if ($paymentDetails['status'] === true) {
            $reference = $paymentDetails['data']['reference'];
            $purchaseId = $paymentDetails['data']['metadata']['purchase_id'];

            $purchase = Purchase::findOrFail($purchaseId);

            if ($purchase->status === 'pending') {
                $wallet = Wallet::where('user_id', Auth::id())->firstOrFail();

                if ($wallet->balance >= $purchase->amount) {
                    try {
                        DB::transaction(function () use ($wallet, $purchase) {
                            $wallet->balance -= $purchase->amount;
                            $wallet->save();

                            $purchase->status = 'completed';
                            $purchase->save();

                            Bid::where('user_id', Auth::id())
                                ->where('gift_card_id', $purchase->gift_card_id)
                                ->delete();
                        });

                        return redirect()->route('giftcards.index')
                            ->with('success', 'Purchase successful! Wallet balance updated.');
                    } catch (\Exception $e) {
                        return redirect()->route('giftcards.my-bids')
                            ->withErrors(['error' => 'An error occurred: ' . $e->getMessage()]);
                    }
                } else {
                    return redirect()->route('giftcards.my-bids')
                        ->withErrors(['error' => 'Insufficient wallet balance!']);
                }
            }

            return redirect()->route('giftcards.my-bids')
                ->with('info', 'Purchase has already been processed.');
        }

        return redirect()->route('giftcards.my-bids')
            ->withErrors(['error' => 'Payment verification failed.']);
    }



    public function transfer(GiftCard $giftcard)
    {
        $purchase = Purchase::where('gift_card_id', $giftcard->id)
            ->where('buyer_id', Auth::id())
            ->where('status', 'pending')
            ->firstOrFail();

        $bankDetails = [
            'account_name' => 'Charles uwaje',
            'account_number' => 'ACC-677C443BD6C46',
            'bank_name' => 'Chaxo Bank',
            'swift_code' => 'SWIFTCODE',
        ];

        return view('purchases.transfer', compact('purchase', 'bankDetails'));
    }
}


    // public function handlePaystackCallback(Request $request)
    // {
    //     $paystack = Paystack::getInstance();

    //     if ($request->has('reference')) {
    //         $reference = $request->input('reference');

    //         $result = $paystack->getPaymentData();

    //         if ($result['status']) {
    //             $reference = $result['data']['reference'];
    //             $parts = explode('-', $reference);
    //             $purchase_id = $parts[1] ?? null;

    //             if ($purchase_id) {
    //                 $purchase = Purchase::find($purchase_id);
    //                 if ($purchase && $purchase->status === 'pending') {
    //                     $purchase->status = 'completed';
    //                     $purchase->save();

    //                     return redirect()->route('giftcards.index')->with('success', 'Purchase successful!');
    //                 }
    //             }
    //         }
    //     }

    //     return redirect()->route('giftcards.index')->withErrors(['error' => 'Payment failed or invalid.']);
    // }

    // public function handlePaystackCallback(Request $request)
    // {
    //     try {
    //         $paymentDetails = Paystack::getPaymentData();

    //         if ($paymentDetails['status']) {
    //             $reference = $paymentDetails['data']['reference'];
    //             $parts = explode('-', $reference);
    //             $purchase_id = $parts[1] ?? null;

    //             if ($purchase_id) {
    //                 $purchase = Purchase::find($purchase_id);
    //                 if ($purchase && $purchase->status === 'pending') {
    //                     $purchase->status = 'completed';
    //                     $purchase->save();

    //                     return redirect()->route('giftcards.index')->with('success', 'Purchase successful!');
    //                 }
    //             }
    //         }

    //         return redirect()->route('giftcards.index')->withErrors(['error' => 'Payment verification failed.']);
    //     } catch (\Exception $e) {
    //         return redirect()->route('giftcards.index')->withErrors(['error' => 'An error occurred: ' . $e->getMessage()]);
    //     }
    // }