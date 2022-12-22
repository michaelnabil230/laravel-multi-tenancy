<?php

namespace MichaelNabil230\MultiTenancy\Tests\TestClasses;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use MichaelNabil230\MultiTenancy\Traits\HasOneTenant;

class User extends Authenticatable
{
    use HasFactory, HasOneTenant;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
}
