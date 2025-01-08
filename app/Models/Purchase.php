<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'gift_card_id',
        'buyer_id',
        'amount',
        'payment_method',
        'status',
    ];

    public function giftCard()
    {
        return $this->belongsTo(GiftCard::class);
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }
}
