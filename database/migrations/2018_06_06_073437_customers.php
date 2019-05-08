<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Customers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::create('customers', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('name')->default('');
            $table->string('email')->default("");
            $table->string('city')->default("");
            $table->string('mobile')->default("");
            $table->string('password')->default("");
            $table->tinyInteger('status')->default("0");
            $table->string('image')->default("");
            $table->string('otp')->default("");
            $table->string('device_token')->default("");
            $table->string('device_id')->default("");
         
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customers');
    }
}
