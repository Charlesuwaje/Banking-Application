<?php

namespace App\Enums;

enum TransactionTypeEnum: string
{
    case DEPOSIT = 'deposit';
    case TRANSFER = 'transfer';
    case WITHDRAW = 'withdraw';
}
