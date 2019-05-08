<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Driver extends Model
{
    
    protected $table = 'drivers';
    protected $softDelete = true;


    protected $fillable = [
        'first_name', 'last_name', 'gender','email', 'mobile', 'country', 'state', 'city', 'zip_code', 'address' , 'password' , 'profile_image' , 'driving_licence' , 'licence_number' , 'deleted_at' , 'is_registered'
    ];
}
