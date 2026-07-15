<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('themes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('theme_category_id')->constrained('theme_categories')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('component_key')->nullable();
            $table->string('thumbnail')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            // 'price' dihapus, karena harga sekarang melekat di theme_categories (paket)
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('themes');
    }
};
