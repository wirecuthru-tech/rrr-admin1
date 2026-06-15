<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class TeamPost extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'team_posts';

    protected $fillable = [
        'real_id','post_id','parent_post_id',
        'name','email','phone','role',
        'badge_name','badge_icon','country','status','is_primary',
        'permissions',
        'owner_post_id','assistant_owner_post_id','country_manager_post_id',
        'super_admin_post_id','bd_post_id','agency_post_id','host_post_id',
    ];

    protected $casts = [
        'permissions' => 'array',
        'is_primary' => 'boolean',
    ];
}