<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VolunteerApplication extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'country',
        'skills',
        'availability',
        'nrc_path',
        'resume_path',
        'message',
        'status',
    ];
}
