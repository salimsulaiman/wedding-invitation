<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invitation_couples', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invitation_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['groom', 'bride']); // pria / wanita
            $table->string('full_name');
            $table->string('nickname')->nullable();
            $table->string('father_name')->nullable();
            $table->string('mother_name')->nullable();
            $table->unsignedTinyInteger('child_order')->nullable(); // anak keberapa
            $table->string('photo')->nullable();
            $table->timestamps();

            $table->unique(['invitation_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invitation_couples');
    }
};
