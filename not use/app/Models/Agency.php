<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Agency extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'agencies';

    protected $fillable = [
        'agency_name',
        'owner_name',
        'mobile',
        'email',
        'password',
        'commission',
        'country',
        'status',
        'wallet'
    ];
}