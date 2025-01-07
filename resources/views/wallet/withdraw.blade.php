@extends('layouts.app')

@section('content')

<div class="container mt-5">
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="card shadow-lg">
                <div class="card-header bg-danger text-white text-center">
                    <h3>Withdraw Funds</h3>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('wallet.withdraw') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="withdraw_amount" class="form-label">Amount</label>
                            <input type="number" name="amount" id="withdraw_amount" class="form-control" placeholder="Enter amount to withdraw" required>
                            @error('amount')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="bank" class="form-label">Select Bank</label>
                            <select name="bank" id="bank" class="form-control" required>
                                <option value="" disabled selected>Select Bank</option>
                                {{-- <option value="Bank A">Bank A</option>
                                <option value="Bank B">Bank B</option>
                                <option value="Bank C">Bank C</option> --}}
                                @foreach ($banks as $bank)
                                    <option value="{{ $bank->id }}">
                                        {{ $bank->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('bank')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="bank_logo" class="form-label">Bank Logo</label>
                            <div id="bank_logo" class="d-flex align-items-center">
                                @foreach ($banks as $bank)
                                    <div class="me-2">
                                        {{-- <img src="{{ asset('storage/' . $bank->logo) }}" alt="{{ $bank->name }}" width="50" class="rounded"> --}}
                                        <img src="{{ asset($bank->logo) }}" alt="{{ $bank->name }}" width="23" class="rounded">

                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <button type="submit" class="btn btn-danger w-100">Withdraw</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection 
