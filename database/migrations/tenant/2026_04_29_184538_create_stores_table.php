<?php

use App\Enums\Status;
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
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->float('lat')->nullable();
            $table->float('long')->nullable();
            $table->float('radius')->nullable();
            $table->enum('status',Status::cases())->default(Status::ACTIVE);
            $table->enum('type',Type::cases())->default(Type::STORE);
            $table->timestamps();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->foreign('store_id')->references('id')->on('stores')->cascadeOnUpdate()->nullOnDelete();
            $table->foreign('parent_id')->references('id')->on('products')->cascadeOnUpdate()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};
