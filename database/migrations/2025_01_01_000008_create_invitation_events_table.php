<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invitation_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invitation_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['akad', 'resepsi']);
            $table->date('date')->nullable();
            $table->time('time')->nullable();
            $table->string('place')->nullable();     // nama tempat
            $table->text('address')->nullable();      // alamat lengkap
            $table->timestamps();

            $table->unique(['invitation_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invitation_events');
    }
};
