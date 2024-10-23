<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('histories', function (Blueprint $table) {
            $table->id();
            $table->date('history_updated')->nullable();
            $table->string('medical_history')->nullable();
            $table->string('ocular_history')->nullable();
            $table->unsignedBigInteger('patient_id')->nullable(); // Add the foreign key column
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('set null'); // Define the foreign key constraint
            $table->timestamps();
            $table->string('date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('histories');
    }
}
