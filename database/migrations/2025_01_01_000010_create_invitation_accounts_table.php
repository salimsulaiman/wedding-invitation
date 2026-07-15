<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invitation_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invitation_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['bank', 'ewallet'])->default('bank');
            $table->string('bank_name');       // nama bank / ewallet
            $table->string('account_number');
            $table->string('account_holder');  // atas nama
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invitation_accounts');
    }
};
