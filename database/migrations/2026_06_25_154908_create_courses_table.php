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
    Schema::create('courses', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->string('slug')->unique();
        $table->text('short_description')->nullable();
        $table->longText('full_description')->nullable();
        $table->decimal('price', 10, 2)->default(0.00);
        $table->string('thumbnail_path')->nullable();
        $table->boolean('is_published')->default(false);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
