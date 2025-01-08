@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <h1>My Gift Card Bids</h1>
        @if ($bids->isEmpty())
            <p class="text-muted">You haven't placed any bids yet.</p>
        @else
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Gift Card</th>
                        <th>Bid Amount</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($bids as $bid)
                        <tr>
                            <td>{{ $bid->id }}</td>
                            <td>{{ $bid->giftcard->name }}</td>
                            <td>${{ number_format($bid->amount, 2) }}</td>
                            <td>{{ ucfirst($bid->status) }}</td>
                            <td>
                                <a href="{{ route('purchase.form', ['giftcard' => $bid->giftcard->id]) }}"
                                    class="btn btn-primary btn-sm">
                                    Purchase
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <!-- Display Pagination Links -->
            <div class="d-flex justify-content-center">
                {{ $bids->links() }}
            </div>
        @endif
    </div>
@endsection
