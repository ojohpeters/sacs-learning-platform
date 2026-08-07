<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Per-lesson override for the minimum active seconds required before the
     * lesson can be marked complete. NULL = use the content-aware default;
     * 0 = no time gate.
     */
    public function up(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->unsignedInteger('min_seconds')->nullable()->after('duration');
        });
    }

    public function down(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->dropColumn('min_seconds');
        });
    }
};
