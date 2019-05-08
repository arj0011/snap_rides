<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class AppCity extends Authenticatable
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
    protected $table = 'app_city';
    
}
