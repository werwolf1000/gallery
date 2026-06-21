<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('address');
            $table->string('category')->default('apartment');
            $table->string('type')->nullable();
            $table->unsignedInteger('area')->nullable();
            $table->unsignedTinyInteger('rooms')->nullable();
            $table->unsignedSmallInteger('floor')->nullable();
            $table->unsignedBigInteger('price');
            $table->string('bonus')->nullable();
            $table->string('image_url')->nullable();
            $table->string('badge')->nullable();
            $table->string('status')->default('for_sale');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
