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
        Schema::create('detail_transfers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transfer_id');
            $table->unsignedBigInteger('kardex_id');
            $table->string('product_id');
            $table->unsignedBigInteger('quantity');
            $table->unsignedBigInteger('store_id');
            $table->timestamps();

            $table->foreign('transfer_id')->references('id')->on('transfers')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('kardex_id')->references('id')->on('kardexes')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('store_id')->references('id')->on('stores')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('product_id')->references('id')->on('products')->cascadeOnUpdate()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_transfers');
    }
};
