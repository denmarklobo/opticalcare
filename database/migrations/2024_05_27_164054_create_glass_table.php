<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGlassTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('glasses', function (Blueprint $table) {
            $table->id();
            $table->string('frame')->nullable();
            $table->string('type_of_lens')->nullable();
            $table->text('remarks')->nullable();
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
        Schema::dropIfExists('glasses');
    }
}
