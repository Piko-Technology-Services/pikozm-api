<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartnerApplication extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'country',
        'partnership_type',
        'organization_type',
        'message',
        'status',
    ];
}
