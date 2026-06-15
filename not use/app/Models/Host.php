<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Host extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'hosts';

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'mobile',
        'gender',
        'country',
        'profile_image',
        'status',
        'diamonds',
        'monthly_earning',
        'approved_at',
        'rejected_at'
    ];
}