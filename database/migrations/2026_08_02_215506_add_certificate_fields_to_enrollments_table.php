<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A public, unguessable code used to verify a completion certificate,
     * plus when it was issued.
     */
    public function up(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->string('certificate_code')->nullable()->unique();
            $table->timestamp('certificate_issued_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropUnique(['certificate_code']);
            $table->dropColumn(['certificate_code', 'certificate_issued_at']);
        });
    }
};
