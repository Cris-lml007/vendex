<?php

use App\Enums\Status;
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
            // $table->id();
            $table->string('id')->unique();
            $table->string('name');
            $table->string('brand');
            $table->string('model');
            $table->string('description')->nullable();
            $table->decimal('price');

            $table->boolean('is_serialize')->default(false);
            $table->string('parent_id')->nullable();

            $table->enum('status',Status::cases())->default(Status::ACTIVE);
            $table->unsignedBigInteger('category_id')->nullable();
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
