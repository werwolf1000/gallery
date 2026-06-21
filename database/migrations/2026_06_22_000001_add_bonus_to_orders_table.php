<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('bonus')->nullable()->after('message');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['property_id']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('property_id')->nullable()->change();
            $table->foreign('property_id')->references('id')->on('properties')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['property_id']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('property_id')->nullable(false)->change();
            $table->foreign('property_id')->references('id')->on('properties')->cascadeOnDelete();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('bonus');
        });
    }
};
