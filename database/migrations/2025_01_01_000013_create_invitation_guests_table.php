<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invitation_guests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invitation_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('group')->nullable();       // keluarga, teman kantor, dll
            $table->string('slug')->unique();           // dipakai di link ?to=slug
            $table->unsignedInteger('quota')->default(1); // jumlah org yg diundang dlm 1 tamu
            $table->boolean('is_sent')->default(false);
            $table->timestamp('opened_at')->nullable();  // kapan tamu buka undangan
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invitation_guests');
    }
};
