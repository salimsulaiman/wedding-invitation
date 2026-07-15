<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invitation_gift_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invitation_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('receiver_name')->nullable();
            $table->string('phone')->nullable();
            $table->text('address');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invitation_gift_addresses');
    }
};
