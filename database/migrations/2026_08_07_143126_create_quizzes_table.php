<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->enum('type', ['section_quiz', 'final_exam'])->default('section_quiz');
            $table->text('description')->nullable();
            $table->integer('passing_score')->default(70); // percentage
            $table->integer('time_limit')->nullable(); // minutes, null = no limit
            $table->integer('min_submit_time')->default(5); // minutes before can submit
            $table->boolean('show_correct_answers')->default(true); // show answers after fail
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quizzes');
    }
};