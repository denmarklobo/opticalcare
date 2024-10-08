<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePatientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('full_name'); 
            $table->string('email')->unique();
            $table->string('address')->nullable();
            $table->string('contact')->nullable();
            $table->date('birthdate')->nullable();
            $table->string('password');
            $table->timestamps();
            $table->unsignedBigInteger('user_id')->nullable();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
         
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('patients');
    }
}
