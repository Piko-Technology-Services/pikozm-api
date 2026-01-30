<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    protected $fillable = [
    'name',
    'email',
    'focus_area',
    'amount',
    'currency',
    'reference',
    'message',
    'status',
    'payment_response',
];

}