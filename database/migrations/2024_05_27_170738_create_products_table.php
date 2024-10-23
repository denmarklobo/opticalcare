<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('product_name');
            $table->string('supplier');
            $table->json('color_stock');
            $table->integer('quantity');
            $table->decimal('price', 10, 2);
            $table->json('image')->nullable();
            // Adding ENUM fields for gender and type
            $table->enum('gender', ['Men', 'Women', 'Unisex']);
            $table->enum('type', ['Frames', 'Lens', 'Contact Lenses', 'Accessories']);
            $table->timestamps();
            $table->integer('new_stock_added')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
