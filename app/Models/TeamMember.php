<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class TeamMember extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'team_members';

    protected $fillable = [
        'real_id',
        'post_id',
        'parent_post_id',
        'name',
        'email',
        'phone',
        'password',
        'role',
        'country',
        'status',
        'permissions',
        'owner_post_id',
        'assistant_owner_post_id',
        'country_manager_post_id',
        'super_admin_post_id',
        'bd_post_id',
        'agency_post_id',
        'host_post_id',
    ];

    protected $casts = [
        'permissions' => 'array',
    ];
}