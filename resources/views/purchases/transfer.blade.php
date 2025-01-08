@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Bank Transfer Instructions</h2>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-body">
            <p>Please transfer the amount of <strong>${{ number_format($purchase->amount, 2) }}</strong> to the following bank account:</p>

            <ul>
                <li><strong>Account Name:</strong> {{ $bankDetails['account_name'] }}</li>
                <li><strong>Account Number:</strong> {{ $bankDetails['account_number'] }}</li>
                <li><strong>Bank Name:</strong> {{ $bankDetails['bank_name'] }}</li>
                <li><strong>SWIFT Code:</strong> {{ $bankDetails['swift_code'] }}</li>
            </ul>

            <p>After completing the transfer, please contact support to confirm your payment.</p>
        </div>
    </div>
</div>
@endsection
