<?php

namespace App\Models;


class Host extends BaseMongoModel
{
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