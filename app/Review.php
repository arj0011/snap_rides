<?php

namespace App;

use App\Model\Product;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
	protected $fillable = [
		'star','customer','review'
	];
}