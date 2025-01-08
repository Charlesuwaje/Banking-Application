<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\GiftCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notifications\BidAcceptedNotification;

class GiftCardController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

    // public function index()
    // {
    //     $giftCards = GiftCard::where('status', 'active')
    //         ->where('end_time', '>', Carbon::now())
    //         ->orderBy('end_time', 'asc')
    //         ->paginate(10);

    //     return view('giftcards.index', compact('giftCards'));
    // }

    public function index()
    {
        $giftCards = GiftCard::where('status', 'active')
            ->where('end_time', '>', Carbon::now())
            ->orderBy('end_time', 'asc')
            ->paginate(10);

        return view('giftcards.index', compact('giftCards'));
    }

    public function create()
    {
        return view('giftcards.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'starting_bid' => 'required|numeric|min:1',
            'end_time' => 'required|date|after:now',
        ]);

        GiftCard::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'starting_bid' => $request->starting_bid,
            'current_bid' => $request->starting_bid,
            'end_time' => $request->end_time,
            'status' => 'active',
        ]);

        return redirect()->route('giftcards.index')->with('success', 'Gift Card auction created successfully.');
    }

    public function show(GiftCard $giftcard)
    {
        $giftcard->load(['bids.bidder', 'owner']);

        return view('giftcards.show', compact('giftcard'));
    }
    //     public function acceptBid(Request $request, $giftCardId, $bidId)
    // {
    //     $giftCard = GiftCard::findOrFail($giftCardId);

    //     if ($giftCard->user_id !== auth()->id()) {
    //         return response()->json(['error' => 'Unauthorized'], 403);
    //     }

    //     $bid = $giftCard->bids()->where('id', $bidId)->firstOrFail();

    //     $bid->status = 'accepted';
    //     $bid->save();

    //     $giftCard->bids()->where('id', '!=', $bidId)->update(['status' => 'rejected']);

    //     $giftCard->status = 'sold';
    //     $giftCard->save();

    //     $bid->user->notify(new BidAcceptedNotification($bid));

    //     return response()->json(['message' => 'Bid accepted, the bidder can now purchase the gift card.'], 200);
    // }

//     public function acceptBid(Request $request, $giftCardId, $bidId)
//     {
//         $giftCard = GiftCard::findOrFail($giftCardId);

//         // Check if the authenticated user is the owner of the gift card
//         if ($giftCard->user_id !== auth()->id()) {
//             return response()->json(['error' => 'Unauthorized action.'], 403);
//         }

//         // Retrieve the bid by ID
//         $bid = $giftCard->bids()->where('id', $bidId)->firstOrFail();

//         // Update the bid status to accepted
//         $bid->update(['status' => 'accepted']);

//         // Reject all other bids for the gift card
//         $giftCard->bids()->where('id', '!=', $bidId)->update(['status' => 'rejected']);

//         // Update the gift card status to sold
//         $giftCard->update(['status' => 'sold']);

//         // Notify the bidder about the acceptance
//         $bid->user->notify(new BidAcceptedNotification($bid));

//         return response()->json([
//             'message' => 'Bid accepted. The bidder can now purchase the gift card.',
//             'bidder' => $bid->user->name,
//             'amount' => $bid->amount,
//         ], 200);
//     }

//     public function rejectBid(Request $request, $giftCardId, $bidId)
// {
//     $giftCard = GiftCard::findOrFail($giftCardId);

//     if ($giftCard->user_id !== auth()->id()) {
//         return response()->json(['error' => 'Unauthorized'], 403);
//     }

//     $bid = $giftCard->bids()->where('id', $bidId)->firstOrFail();

//     if ($bid->status === 'rejected') {
//         return response()->json(['message' => 'Bid is already rejected.'], 200);
//     }

//     $bid->status = 'rejected';
//     $bid->save();

//     // $bid->user->notify(new BidRejectedNotification($bid)); // Notify the user about the rejection

//     return response()->json(['message' => 'Bid rejected successfully.'], 200);
// }

}
