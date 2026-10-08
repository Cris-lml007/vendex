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
        Schema::create('releases', function (Blueprint $table) {
            $table->id();
            $table->string('version');
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('features')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->index(['active', 'published_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('releases');
    }
};
