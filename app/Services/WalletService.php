<?php

namespace App\Services;

use App\Enums\StatusEnum;
use App\Enums\TransactionTypeEnum;
use App\Models\Wallet;
use App\Models\Transaction;
use App\Models\User;
use Exception;

class WalletService
{
    public function createWallet($userId)
    {
        return Wallet::create(['user_id' => $userId]);
    }


    public function deposit($accountNumber, $amount)
    {
        $user = User::where('account_number', $accountNumber)->firstOrFail();

        $wallet = Wallet::firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0]
        );

        $wallet->balance += $amount;
        $wallet->save();

        Transaction::create([
            'user_id' => $user->id,
            // 'type' => 'deposit',
            'type'=> TransactionTypeEnum::DEPOSIT->value,
            'status'=> StatusEnum::COMPLETED->value,
            'amount' => $amount,
            'description' => 'Deposit to wallet via account number',
        ]);
    }


    // public function withdraw($userId, $amount)
    // {
    //     $wallet = Wallet::where('user_id', $userId)->firstOrFail();

    //     if ($wallet->balance < $amount) {
    //         throw new Exception('Insufficient balance.');
    //     }

    //     $wallet->balance -= $amount;
    //     $wallet->save();

    //     Transaction::create([
    //         'user_id' => $userId,
    //         // 'type' => 'withdrawal',
    //         'type'=> TransactionTypeEnum::WITHDRAW->value,

    //         'amount' => $amount,
    //         'description' => 'Withdrawal from wallet',
    //     ]);
    // }

    public function withdraw($userId, $amount)
    {
        $wallet = Wallet::where('user_id', $userId)->firstOrFail();
    
        if ($wallet->balance < $amount) {
            throw new \RuntimeException('Insufficient balance in your wallet.');
        }
    
        $wallet->balance -= $amount;
        $wallet->save();
    
        Transaction::create([
            'user_id' => $userId,
            'type' => TransactionTypeEnum::WITHDRAW->value,
            'amount' => $amount,
            'description' => 'Withdrawal from wallet',
        ]);
    }
    

    public function transfer($fromUserId, $toUserId, $amount)
    {
        $fromWallet = Wallet::where('user_id', $fromUserId)->firstOrFail();
        $toWallet = Wallet::where('user_id', $toUserId)->firstOrFail();

        if ($fromWallet->balance < $amount) {
            // throw new Exception('Insufficient balance for transfer.');
            throw new \RuntimeException('Insufficient balance for transfer.');
        }

        $fromUser = $fromWallet->user;
        $toUser = $toWallet->user;  

        $fromWallet->balance -= $amount;
        $fromWallet->save();

        $toWallet->balance += $amount;
        $toWallet->save();

        Transaction::create([
            'user_id' => $fromUserId,
            'to_user_id' => $toUserId,
            // 'type' => 'transfer',
            'type'=> TransactionTypeEnum::TRANSFER->value,
            'amount' => $amount,
            'description' => sprintf(
                'Transfer of $%s from %s to  %s',
                number_format($amount, 2),
                $fromUser->name,
                $toUser->name,
                $toUser->account_number
            ),
        ]);
    }
}
