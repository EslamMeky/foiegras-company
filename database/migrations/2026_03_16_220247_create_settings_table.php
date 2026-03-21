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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('logo');
            $table->string('slug_ar');
            $table->string('slug_en');
            $table->text('desc_ar');
            $table->text('desc_en');
            $table->string('face')->nullable();
            $table->string('insta')->nullable();
            $table->string('tiktok')->nullable();
            $table->string('whats')->nullable();
            $table->string('location_ar');
            $table->string('location_en');
            $table->string('phone');
            $table->string('email');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
