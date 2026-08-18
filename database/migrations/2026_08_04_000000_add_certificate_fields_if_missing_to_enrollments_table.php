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
        if (! Schema::hasColumn('enrollments', 'certificate_code')) {
            Schema::table('enrollments', function (Blueprint $table) {
                $table->string('certificate_code')->nullable()->unique();
            });
        }

        if (! Schema::hasColumn('enrollments', 'certificate_issued_at')) {
            Schema::table('enrollments', function (Blueprint $table) {
                $table->timestamp('certificate_issued_at')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {   
        if (Schema::hasColumn('enrollments', 'certificate_issued_at')) {
            Schema::table('enrollments', function (Blueprint $table) {
                $table->dropColumn('certificate_issued_at');
            });
        }

        if (Schema::hasColumn('enrollments', 'certificate_code')) {
            Schema::table('enrollments', function (Blueprint $table) {
                $table->dropUnique(['certificate_code']);
                $table->dropColumn('certificate_code');
            });
        }
    }
};
