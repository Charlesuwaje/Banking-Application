<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bid extends Model
{
    use HasFactory;

    protected $fillable = [
        'gift_card_id',
        'user_id',
        'amount',
    ];

    public function giftCard()
    {
        return $this->belongsTo(GiftCard::class);
    }
    public function bidder()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
