<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

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

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public static $login_rules = array(
        'email'=>'required',
        'password'=>'required'
    );

    public static $add_rules = array(
        'name'=>'required',
        'username'=>'required',
        'password'=>'required',
        'role'=>'required',
    );

    public static $edit_rules = array(
        'name'=>'required',
        'email'=>'required',
        'role'=>'required',
    );

    public static $update_password_rules = array(
        'email'=>'required|exists:users',
        'password'=>'required|confirmed',
        'password_confirmation'=>'required'
    );

}
