<?php
namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class BaseMongoModel extends Model
{
    protected $connection = 'mongodb';
    protected $primaryKey = '_id';
    protected $guarded = [];
}
