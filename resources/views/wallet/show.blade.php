@extends('layouts.app')

@section('content')
    <h2 class="text-center">Your Wallet</h2>

    <!-- Wallet Card -->
    <div class="card mb-4">
        <div class="card-body text-center">
            <h4>
                Balance:
                <span id="balance" style="display: inline;">${{ number_format($balance, 2) }}</span>
                <span id="balance-hidden" style="display: none;">**********</span>
                <button id="toggle-visibility" class="btn btn-sm btn-light">
                    <i id="visibility-icon" class="bi bi-eye"></i>
                </button>
            </h4>
            <p>Account Number: {{ auth()->user()->account_number }}</p>
        </div>
    </div>

    <!-- Deposit Form -->
    <h4>Deposit</h4>
    <form method="POST" action="{{ route('wallet.deposit') }}">
        @csrf
        <div class="mb-3">
            <label for="account_number" class="form-label">Account Number</label>
            <input type="text" name="account_number" id="account_number" class="form-control"
                placeholder="Enter Account Number" required>
        </div>
        <div class="mb-3">
            <label for="user_name" class="form-label">User Name</label>
            <input type="text" id="user_name" class="form-control" placeholder="User Name" readonly>
        </div>
        <div class="mb-3">
            <label for="amount" class="form-label">Amount</label>
            <input type="number" name="amount" id="amount" class="form-control" placeholder="Enter Amount" required>
        </div>
        <button type="submit" class="btn btn-success w-100">Deposit</button>
    </form>

    <!-- Transaction History -->
    <h4 class="mt-4">Transaction History</h4>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Type</th>
                <th>Transaction method</th>
                <th>Amount</th>
                <th>Description</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($transactions as $transaction)
                <tr>
                    <td>
                        @if ($transaction->user_id === auth()->id())
                            Sent
                        @elseif ($transaction->to_user_id === auth()->id())
                            Received
                        @endif
                    </td>
                    <td>{{ $transaction->type }}</td>
                    <td>${{ number_format($transaction->amount, 2) }}</td>
                    <td>{{ $transaction->description }}</td>
                    <td>{{ ucfirst($transaction->status) }}</td>
                    <td>{{ $transaction->created_at->format('Y-m-d H:i') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">No transactions found</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Pagination Links -->
    <div class="mt-3">
        {{ $transactions->links('vendor.pagination.bootstrap-5') }}
    </div>
@endsection

{{-- @section('scripts') --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            function toggleVisibility() {
                const balanceVisible = document.getElementById('balance');
                const balanceHidden = document.getElementById('balance-hidden');
                const visibilityIcon = document.getElementById('visibility-icon');

                if (balanceVisible.style.display === 'inline') {
                    console.log('Toggling balance visibility');
                    balanceVisible.style.display = 'none';
                    balanceHidden.style.display = 'inline';
                    visibilityIcon.classList.remove('bi-eye');
                    visibilityIcon.classList.add('bi-eye-slash');
                } else {
                    balanceVisible.style.display = 'inline';
                    balanceHidden.style.display = 'none';
                    visibilityIcon.classList.remove('bi-eye-slash');
                    visibilityIcon.classList.add('bi-eye');
                }
            }

            const toggleButton = document.getElementById('toggle-visibility');
            toggleButton.addEventListener('click', toggleVisibility);

            const accountNumberField = document.getElementById('account_number');
            const userNameField = document.getElementById('user_name');

            accountNumberField.addEventListener('blur', function() {
                const accountNumber = this.value;

                if (accountNumber) {
                    fetch(`/users/${accountNumber}`)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('User not found');
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                userNameField.value = data.user.name;
                            } else {
                                userNameField.value = 'User not found';
                            }
                        })
                        .catch(() => {
                            userNameField.value = 'Error fetching user';
                        });
                } else {
                    userNameField.value = '';
                }
            });
        });
    </script>
{{-- @endsection --}}
