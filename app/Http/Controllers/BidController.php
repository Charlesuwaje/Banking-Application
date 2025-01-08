<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GiftCard;
use App\Models\Bid;
use Illuminate\Support\Facades\Auth;
use Exception;

class BidController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request, GiftCard $giftcard)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        if ($giftcard->status !== 'active' || $giftcard->end_time <= now()) {
            return back()->withErrors(['error' => 'Auction has ended or is inactive.']);
        }

        $minBid = $giftcard->current_bid + 1;

        if ($request->amount < $minBid) {
            return back()->withErrors(['amount' => 'Your bid must be at least $' . $minBid]);
        }

        $user = Auth::user();
        if ($user->wallet->balance < $request->amount) {
            return back()->withErrors(['amount' => 'Insufficient wallet balance for this bid.']);
        }

        try {
            $user->wallet->balance -= $request->amount;
            $user->wallet->save();

            Bid::create([
                'gift_card_id' => $giftcard->id,
                'user_id' => $user->id,
                'amount' => $request->amount,
            ]);

            $giftcard->current_bid = $request->amount;
            $giftcard->save();

            return back()->with('success', 'Your bid has been placed successfully.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'An error occurred while placing your bid.']);
        }
    }

    //     public function placeBid(Request $request, $id)
    // {
    //     $giftCard = GiftCard::findOrFail($id);

    //     if ($giftCard->status !== 'active') {
    //         return back()->with('error', 'This auction is not active.');
    //     }

    //     $request->validate([
    //         'bid' => 'required|numeric|min:' . ($giftCard->current_bid + 1),
    //     ]);

    //     $giftCard->current_bid = $request->input('bid');
    //     $giftCard->save();

    //     return back()->with('success', 'Your bid has been placed successfully!');
    // }

    // public function placeBid(Request $request, $id)
    // {
    //     $giftCard = GiftCard::findOrFail($id);

    //     if ($giftCard->status !== 'active') {
    //         return back()->with('error', 'This auction is not active.');
    //     }

    //     $request->validate([
    //         'amount' => 'required|numeric|min:' . ($giftCard->current_bid + 1),
    //     ]);

    //     $giftCard->current_bid = $request->input('amount');
    //     $giftCard->save();

    //     return back()->with('success', 'Your bid has been placed successfully!');
    // }

    public function placeBid(Request $request, $id)
    {
        $giftCard = GiftCard::findOrFail($id);

        if ($giftCard->status !== 'active') {
            return back()->with('error', 'This auction is not active.');
        }

        $request->validate([
            'amount' => 'required|numeric|min:' . ($giftCard->current_bid + 1),
        ]);

        $bid = new Bid();
        $bid->gift_card_id = $giftCard->id;
        $bid->user_id = auth()->id();
        $bid->amount = $request->input('amount');
        $bid->save();

        $giftCard->current_bid = $bid->amount;
        $giftCard->save();

        return back()->with('success', 'Your bid has been placed successfully!');
    }

    public function getMyBids()
    {
        $user = auth()->user();
    
        if (!$user) {
            return redirect()->route('login')->with('error', 'You need to be logged in to view your bids.');
        }
    
        // $bids = $user->giftcardBids()->with('giftcard')->get();
        $bids = $user->giftcardBids()->with('giftcard')->paginate(10);
        return view('giftcards.my-bids', compact('bids'));
    }
}
