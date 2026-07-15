<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('domains', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invitation_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('name')->unique(); // slug custom domain, cth: nama-domain-custom
            $table->boolean('is_active')->default(true); // admin bisa nonaktifkan kapan saja
            $table->timestamp('activated_at')->nullable();
            $table->timestamp('deactivated_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('domains');
    }
};
