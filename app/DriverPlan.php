<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DriverPlan extends Model
{  
	use SoftDeletes;
 
    protected $table = 'driver_plan';
    protected $softDelete = true;
}
