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
        Schema::create('exchange_rates', function (Blueprint $table) {
            $table->id();
            $table->decimal('usd_to_bs', 15, 8)->default(0);
            $table->timestamps();
        });

        Schema::table('kardexes', function (Blueprint $table) {
            $table->unsignedInteger('exchange_rate_id')->nullable();
            #$table->foreign('exchange_rate_id')->references('id')->on('exchange_rates');
        });

        Schema::table('detail_transactions', function (Blueprint $table) {
            $table->unsignedInteger('exchange_rate_id')->nullable();
            #$table->foreign('exchange_rate_id')->references('id')->on('exchange_rates')->cascadeOnUpdate()->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exchange_rates');
    }
};
