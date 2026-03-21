<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->integer('category_id');
            $table->string('name_ar');
            $table->string('name_en');
            $table->text('desc_ar');
            $table->text('desc_en');
            $table->double('main_price')->nullable();
            $table->double('price_discount');
            $table->json('weight')->nullable();
            $table->string('note')->nullable();
            $table->integer('stock');
            $table->integer('outOfStock')->nullable()->default(0);
            $table->string('barcode')->nullable();
            $table->string('image');
            $table->json('otherImage')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
