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
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('made');
            $table->string('color_fg');
            $table->string('color_bg');
            $table->timestamps();
        });

        Schema::table('products', function (Blueprint $table) {
           $table->dropColumn('brand');
           $table->unsignedBigInteger('brand_id');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brands');
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('brand_id');
            $table->string('brand');
        });
    }
};
