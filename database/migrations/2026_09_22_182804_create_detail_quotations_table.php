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
        Schema::create('detail_quotations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('quotation_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('quantity');
            $table->decimal('price',15,8);
            $table->timestamps();
        });

        Schema::table('transactions', function (Blueprint $table){
            $table->unsignedBigInteger('quotation_id')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_quotations');
        Schema::table('transactions', function( Blueprint $table ){
            $table->dropColumn('quotation_id');
        });
    }
};
