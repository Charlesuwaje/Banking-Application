@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>{{ $giftcard->name }}</h2>

        <div class="card mb-4">
            <div class="card-body">
                <p><strong>Starting Bid:</strong> ${{ number_format($giftcard->starting_bid, 2) }}</p>
                <p><strong>Current Bid:</strong> ${{ number_format($giftcard->current_bid, 2) }}</p>
                <p><strong>Auction Ends At:</strong> {{ $giftcard->end_time->format('Y-m-d H:i') }}</p>
                <p><strong>Owner:</strong> {{ $giftcard->owner->name }}</p>
                <p><strong>Status:</strong> {{ ucfirst($giftcard->status) }}</p>
            </div>
        </div>

        @if ($giftcard->status === 'active' && $giftcard->end_time > now())
            <h4>Place a Bid</h4>

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- <form action="{{ route('bids.store', $giftcard->id) }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="amount" class="form-label">Your Bid ($)</label>
                <input type="number" name="amount" id="amount" class="form-control" min="{{ $giftcard->current_bid + 1 }}" step="0.01" placeholder="Enter your bid" required>
            </div>

            <button type="submit" class="btn btn-primary">Place Bid</button>
        </form> --}}
            <form action="{{ route('giftcards.placeBid', $giftcard->id) }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="amount" class="form-label">Your Bid ($)</label>
                    <input type="number" name="amount" id="amount" class="form-control"
                        min="{{ $giftcard->current_bid + 1 }}" step="0.01" placeholder="Enter your bid" required>
                </div>

                <button type="submit" class="btn btn-primary">Place Bid</button>
            </form>
        @elseif ($giftcard->status === 'ended')
            <div class="alert alert-info">
                <strong>Auction Ended.</strong>
                @if ($giftcard->bids->count() > 0)
                    <p>Winner: {{ $giftcard->bids->sortByDesc('amount')->first()->bidder->name }} with a bid of
                        ${{ number_format($giftcard->bids->sortByDesc('amount')->first()->amount, 2) }}</p>
                    <a href="{{ route('purchase.form', $giftcard->id) }}" class="btn btn-success">Purchase Gift Card</a>
                @else
                    <p>No bids were placed on this gift card.</p>
                @endif
            </div>
        @endif

        <!-- Transaction History -->
        {{-- <h4 class="mt-5">Transaction History</h4>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Bidder</th>
                <th>Amount ($)</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($giftcard->bids as $bid)
                <tr>
                    <td>{{ $bid->bidder->name }}</td>
                    <td>${{ number_format($bid->amount, 2) }}</td>
                    <td>{{ $bid->created_at->format('Y-m-d H:i') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center">No bids yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table> --}}

        <!-- Transaction History -->
        <h4 class="mt-5">Gift Card  History</h4>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Bidder</th>
                    <th>Amount ($)</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($giftcard->bids as $bid)
                    <tr>
                        <td>{{ $bid->bidder->name }}</td>
                        <td>${{ number_format($bid->amount, 2) }}</td>
                        <td>{{ $bid->created_at->format('Y-m-d H:i') }}</td>
                        <td>
                            <a href="{{ route('purchase.form', $giftcard->id) }}" class="btn btn-success btn-sm">
                                Purchase
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">No bids yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination for Bids (if needed) -->
        {{ $giftcard->bids()->paginate(10)->links() }}
    </div>
@endsection
