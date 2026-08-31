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
            $table->boolean('wholesale_price')->default(false);
            $table->boolean('serialized_products')->default(false);
            $table->boolean('heredaded_products')->default(false);
            $table->boolean('transfers_all')->default(false);
            $table->boolean('change_password')->default(false);
            $table->boolean('tutorial')->default(true);
            $table->string('theme')->default('vendex');
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
