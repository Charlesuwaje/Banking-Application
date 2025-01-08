@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Create New Gift Card Auction</h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('giftcards.store') }}" method="POST">
            @csrf

            <!-- Gift Card Name -->
            <div class="mb-3">
                <label for="name" class="form-label">Gift Card Name</label>
                <input type="text" name="name" id="name" class="form-control" placeholder="Enter gift card name"
                    required>
            </div>

            <!-- Starting Bid -->
            <div class="mb-3">
                <label for="starting_bid" class="form-label">Starting Bid ($)</label>
                <input type="number" name="starting_bid" id="starting_bid" class="form-control"
                    placeholder="Enter starting bid" min="1" step="0.01" required>
            </div>

            <!-- Auction End Time -->
            <div class="mb-3">
                <label for="end_time" class="form-label">Auction End Time</label>
                <input type="datetime-local" name="end_time" id="end_time" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-danger">Create Auction</button>
        </form>
    </div>
@endsection
