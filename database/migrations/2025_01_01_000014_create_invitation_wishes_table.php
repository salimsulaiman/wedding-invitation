<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invitation_wishes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invitation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('guest_id')->nullable()->constrained('invitation_guests')->nullOnDelete();
            $table->string('name');       // bisa isi manual walau tanpa guest_id
            $table->text('message');
            $table->enum('attendance', ['hadir', 'tidak_hadir', 'ragu'])->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invitation_wishes');
    }
};
