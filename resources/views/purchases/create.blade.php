@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Purchase Gift Card</h2>

    <div class="card mb-4">
        <div class="card-body">
            <p><strong>Gift Card:</strong> {{ $giftcard->name }}</p>
            <p><strong>Amount:</strong> ${{ number_format($highestBid->amount, 2) }}</p>
            <p><strong>Payment Method:</strong> {{ ucfirst($highestBid->payment_method) }}</p>
        </div>
    </div>

    <form action="{{ route('purchase.store', $giftcard->id) }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="payment_method" class="form-label">Select Payment Method</label>
            <select name="payment_method" id="payment_method" class="form-control" required>
                <option value="" disabled selected>Select Payment Method</option>
                <option value="transfer">Bank Transfer</option>
                <option value="paystack">Paystack</option>
            </select>
            @error('payment_method')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
        <button type="submit" class="btn btn-success">Proceed to Payment</button>
    </form>
    
</div>
@endsection
