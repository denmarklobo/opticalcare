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
            $table->unsignedBigInteger('product_id')->nullable(); // Add the foreign key column
            $table->foreign('product_id')->references('id')->on('products')->onDelete('set null');
            $table->unsignedBigInteger('lens_id')->nullable(); // Add the foreign key column
            $table->foreign('lens_id')->references('id')->on('products')->onDelete('set null');
            $table->decimal('price', 10, 2);
            $table->string('custom_lens')->nullable();
            $table->string('custom_frame')->nullable();
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
