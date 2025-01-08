@extends('layouts.app')

@section('content')
    <div class="container">
        <h2 class="text-center">Active Gift Card Auctions</h2>

        <!-- Success Message -->
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <!-- Create Auction Button -->
        <div class="text-end mb-3">
            <a href="{{ route('giftcards.create') }}" class="btn btn-primary">Create New Auction</a>
        </div>

        <!-- List of Gift Card Auctions -->
        @forelse ($giftCards as $giftcard)
            <div class="card mb-3">
                <div class="card-body">
                    <h4>{{ $giftcard->name }}</h4>
                    <p>Starting Bid: ${{ number_format($giftcard->starting_bid, 2) }}</p>
                    <p>Current Bid: ${{ number_format($giftcard->current_bid, 2) }}</p>
                    <p>Ends At:
                        @if ($giftcard->end_time)
                            {{ Carbon\Carbon::parse($giftcard->end_time)->format('Y-m-d H:i') }}
                        @else
                            Not Set
                        @endif
                    </p>
                    <a href="{{ route('giftcards.show', $giftcard->id) }}" class="btn btn-success">View Auction</a>
                </div>
            </div>
        @empty
            <p class="text-center">No active auctions available at the moment. Check back later!</p>
        @endforelse

        <!-- Pagination Links -->
        <div class="d-flex justify-content-center">
            {{ $giftCards->links() }}
        </div>
    </div>
@endsection
