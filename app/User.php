<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

     /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */

    protected $fillable = [
        'first_name', 'last_name' , 'gender' , 'country' , 'state' , 'city' , 'zip_code' , 'address' ,'password' ,'email','mobile'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

     public function roles()
    {
        return $this->belongsToMany('App\Role');
    }
}
