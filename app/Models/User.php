<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $table = 'users';
    protected $primaryKey = 'user_id';

    protected $fillable = [
        'usn',
        'first_name',
        'last_name',
        'dob',
        'role_id',
        'is_active'
    ];

    protected $hidden = [
        'dob'
    ];

    public $timestamps = false;
}
