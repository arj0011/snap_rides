<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDriversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drivers', function (Blueprint $table) {
            $table->increments('id')->unsigned()->index();
            $table->char('first_name');
            $table->char('last_name');
            $table->tinyInteger('gender')->default("1");
            $table->string('email');
            $table->integer('mobile');
            $table->string('password');
            $table->integer('company_id');
            $table->integer('country');
            $table->integer('state');
            $table->integer('city');
            $table->string('address');
            $table->integer('zip_code')->nullable();
            $table->string('profile_image')->default('unknown.png');
            $table->string('driving_licence');
            $table->string('licence_number')->nullable();
            $table->tinyInteger('is_approved')->default("0");
            $table->tinyInteger('is_blocked')->default("0");
            $table->tinyInteger('is_active')->default("0");
            $table->tinyInteger('is_logined')->default("0");
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('drivers');
    }
}
