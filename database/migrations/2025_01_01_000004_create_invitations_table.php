<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();      // pemilik undangan
            $table->foreignId('theme_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete(); // admin pembuat, jika admin yg buatkan

            $table->string('name');                    // Nama Acara Undangan, cth: The Wedding of Andi & Siti
            $table->text('quote_text')->nullable();     // Kutipan pembuka
            $table->string('quote_source')->nullable(); // Sumber kutipan
            $table->string('maps_link')->nullable();
            $table->string('streaming_link')->nullable();

            $table->unsignedInteger('max_guest')->nullable(); // null = tanpa batasan
            $table->timestamp('expired_at')->nullable();      // null = tanpa expired

            $table->enum('status', ['draft', 'published', 'inactive'])->default('draft');
            $table->boolean('is_active')->default(true);
            $table->timestamp('published_at')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invitations');
    }
};
