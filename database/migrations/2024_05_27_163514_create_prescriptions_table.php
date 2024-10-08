<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrescriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();
            $table->string('left_eye_sphere')->nullable();
            $table->string('right_eye_sphere')->nullable();
            $table->string('left_eye_cylinder')->nullable();
            $table->string('right_eye_cylinder')->nullable();
            $table->string('left_eye_axis')->nullable();
            $table->string('right_eye_axis')->nullable();
            $table->string('reading_add')->nullable();
            $table->string('left_eye_best_visual_acuity')->nullable();
            $table->string('right_eye_best_visual_acuity')->nullable();
            $table->string('PD')->nullable();
            $table->string('date')->nullable();
            $table->unsignedBigInteger('patient_id')->nullable(); // Add the foreign key column
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('set null'); // Define the foreign key constraint
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
        Schema::dropIfExists('prescriptions');
    }
}
