<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('invitation_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('theme_category_id')->nullable()->constrained()->nullOnDelete(); // paket yang dipesan
            $table->decimal('price', 12, 2)->default(0);
            $table->enum('status', ['pending', 'paid', 'cancelled', 'completed'])->default('pending');
            $table->string('order_source')->default('whatsapp');
            $table->foreignId('handled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamp('ordered_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
