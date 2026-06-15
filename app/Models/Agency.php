<?php

namespace App\Models;


class Agency extends BaseMongoModel
{
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