<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['sync'])->default('sync');
            $table->date('session_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('meeting_link')->nullable();
            $table->string('recording_link')->nullable();
            $table->enum('status', ['upcoming', 'live', 'completed', 'cancelled'])->default('upcoming');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_sessions');
    }
};