<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OfferCode extends Model
{
    use SoftDeletes;
    
    protected $table = 'offer_codes';
    protected $softDelete = true; 
}