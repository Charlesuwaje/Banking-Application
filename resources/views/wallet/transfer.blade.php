@extends('layouts.app')

@section('content')

<div class="container mt-5">
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="card shadow-lg">
                <div class="card-header bg-success text-white text-center">
                    <h3>Transfer Funds</h3>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('wallet.transfer') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="account_number" class="form-label">Recipient Account Number</label>
                            <input type="text" name="account_number" id="account_number" class="form-control" placeholder="Enter recipient account number" required>
                            @error('account_number')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="user_name" class="form-label">User Name</label>
                            <input type="text" id="user_name" class="form-control" placeholder="User Name" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="transfer_amount" class="form-label">Amount</label>
                            <input type="number" name="amount" id="transfer_amount" class="form-control" placeholder="Enter amount to transfer" required>
                            @error('amount')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-success w-100">Transfer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

{{-- @section('scripts') --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const accountNumberField = document.getElementById('account_number');
        const userNameField = document.getElementById('user_name');

        // Fetch user name on blur event
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
