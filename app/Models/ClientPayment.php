<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientPayment extends Model
{
    protected $fillable = [
        'full_name',
        'email',
        'amount',
        'currency',
        'purpose',
        'reference',
        'status',
        'payment_response',
    ];

    protected $casts = [
        'payment_response' => 'array',
    ];
}