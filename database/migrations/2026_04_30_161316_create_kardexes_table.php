<?php

use App\Enums\Type;
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
        Schema::create('kardexes', function (Blueprint $table) {
            $table->id();
            $table->string('product_id');
            $table->unsignedBigInteger('store_id');
            $table->enum('type',Type::cases());
            $table->integer('quantity');
            $table->decimal('price');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('referenceable_id')->nullable();
            $table->string('referenceable_type')->nullable();
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('store_id')->references('id')->on('stores')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnUpdate()->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kardexes');
    }
};
