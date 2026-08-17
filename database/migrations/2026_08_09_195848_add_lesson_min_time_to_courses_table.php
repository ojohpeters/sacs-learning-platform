<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            if (!Schema::hasColumn('courses', 'lesson_min_minutes')) {
                $table->integer('lesson_min_minutes')->default(1)->after('async_price');
            }
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            if (Schema::hasColumn('courses', 'lesson_min_minutes')) {
                $table->dropColumn('lesson_min_minutes');
            }
        });
    }
};