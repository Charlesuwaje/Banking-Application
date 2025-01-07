<?php

namespace App\Enums;

enum StatusEnum: string
{
    case COMPLETED = 'completed';
    case PENDING = 'pending';
    case FAILED = 'failed';
}
