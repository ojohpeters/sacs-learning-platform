<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('payments', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->foreignId('course_id')->constrained()->onDelete('cascade');
        $table->foreignId('enrollment_id')->nullable()->constrained()->onDelete('set null');
        $table->string('payment_gateway')->default('paystack');
        $table->string('transaction_reference')->unique();
        $table->decimal('amount', 10, 2);
        $table->string('currency')->default('NGN');
        $table->enum('status', ['pending', 'successful', 'failed', 'refunded'])->default('pending');
        $table->enum('learning_type', ['inclass', 'sync', 'async']);
        $table->timestamp('paid_at')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
