<?php

namespace App\Http\Controllers;

use App\Models\Banks;
use App\Models\Transaction;
use App\Models\User;
use App\Services\WalletService;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    protected $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }


    public function show()
    {
        $user = auth()->user();
        $balance = $user->wallet ? $user->wallet->balance : 0;

        $transactions = Transaction::where('user_id', $user->id)
            ->orWhere('to_user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('wallet.show', compact('balance', 'transactions'));
    }


    public function deposit(Request $request)
    {
        $request->validate([
            'account_number' => 'required|exists:users,account_number',
            'amount' => 'required|numeric|min:1',
        ]);

        $this->walletService->deposit($request->account_number, $request->amount);

        // return redirect()->back()->with('success', 'Deposit successful.');
        return redirect()->route('wallet.show')->with('success', 'Deposit successful.');
    }


    // public function withdraw(Request $request)
    // {
    //     $request->validate(['amount' => 'required|numeric|min:1']);
    //     $this->walletService->withdraw(auth()->id(), $request->amount);

    //     return redirect()->back()->with('success', 'Withdrawal successful.');
    // }
    public function withdraw(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        try {
            $this->walletService->withdraw(auth()->id(), $request->amount);
            // return redirect()->back()->with('success', 'Withdrawal successful.');
            return redirect()->route('wallet.withdraw.form')->with('success', 'Withdrawal successful.');

        } catch (\RuntimeException $e) {
            return redirect()->back()->withErrors(['amount' => $e->getMessage()]);
        }
    }


    // public function transfer(Request $request)
    // {
    //     $request->validate([
    //         'to_user_id' => 'required|exists:users,id',
    //         'amount' => 'required|numeric|min:1',
    //     ]);

    //     $this->walletService->transfer(auth()->id(), $request->to_user_id, $request->amount);

    //     return redirect()->back()->with('success', 'Transfer successful.');
    // }

    public function transfer(Request $request)
    {
        $request->validate([
            'account_number' => 'required|exists:users,account_number',
            'amount' => 'required|numeric|min:1',
        ]);

        $toUser = User::where('account_number', $request->account_number)->firstOrFail();

        try {
            $this->walletService->transfer(auth()->id(), $toUser->id, $request->amount);
            // return redirect()->back()->with('success', 'Transfer successful.');
            return redirect()->route('wallet.transfer.form')->with('success', 'Transfer successful.');
        } catch (\RuntimeException $e) {
            return redirect()->back()->withErrors(['amount' => $e->getMessage()]);
        }

        // return redirect()->back()->with('success', 'Transfer successful.');
    }


    // public function getUserByAccountNumber($accountNumber)
    // {
    //     $user = User::where('account_number', $accountNumber)->first();

    //     if (!$user) {
    //         return response()->json(['error' => 'User not found'], 404);
    //     }

    //     return response()->json(['name' => $user->name]);
    // }

    public function getUserByAccountNumber($accountNumber)
    {
        $user = User::where('account_number', $accountNumber)->first();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }
        // dd($user );

        return response()->json([
            'success' => true,
            'user' => [
                'name' => $user->name,
            ],
        ]);
    }


    public function showWithdrawForm()
    {
        $banks = Banks::all();
        return view('wallet.withdraw', compact('banks'));
    }

    public function showTransferForm()
    {
        return view('wallet.transfer');
    }
}
