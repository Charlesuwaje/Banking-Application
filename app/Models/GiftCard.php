<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GiftCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'starting_bid',
        'current_bid',
        'end_time',
        'status',
    ];

    protected $casts = [
        'end_time' => 'datetime',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function bids()
    {
        return $this->hasMany(Bid::class);
    }

    public function highestBid()
    {
        return $this->hasOne(Bid::class)->orderBy('amount', 'desc');
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }


    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
