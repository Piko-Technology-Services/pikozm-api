<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportRequest extends Model
{
    protected $fillable = [
        'organization_name',
        'contact_person',
        'email',
        'phone',
        'country',
        'organization_type',
        'support_needs',
        'website',
        'message',
        'status',
    ];
}
