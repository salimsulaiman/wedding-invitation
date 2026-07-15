<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('theme_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');            // Basic, Premium, Exclusive, dll
            $table->string('slug')->unique();
            $table->decimal('price', 12, 2)->default(0); // harga paket, cth: Basic = 99000
            $table->text('description')->nullable();
            $table->boolean('allow_custom_photos')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('theme_categories');
    }
};
