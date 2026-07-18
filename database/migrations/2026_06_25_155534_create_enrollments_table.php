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
    Schema::create('enrollments', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->foreignId('course_id')->constrained()->onDelete('cascade');
        $table->enum('learning_type', ['inclass', 'sync', 'async'])->default('async');
        $table->enum('status', ['active', 'completed', 'expired', 'cancelled'])->default('active');
        $table->timestamp('enrolled_at')->useCurrent();
        $table->timestamp('completed_at')->nullable();
        $table->timestamps();
        $table->unique(['user_id', 'course_id', 'learning_type']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
