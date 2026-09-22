<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('candidate', function (Blueprint $table) {
            if (!Schema::hasColumn('candidate', 'position_id')) {
                $table->unsignedBigInteger('position_id')->nullable()->after('course_id');
            }
        });

        Schema::table('candidate', function (Blueprint $table) {
            if (Schema::hasColumn('candidate', 'position_id')) {
                $hasFk = false;

                try {
                    $dbName = DB::getDatabaseName();
                    $rows = DB::select(
                        "SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'candidate' AND COLUMN_NAME = 'position_id' AND REFERENCED_TABLE_NAME IS NOT NULL",
                        [$dbName]
                    );
                    $hasFk = count($rows) > 0;
                } catch (\Throwable $e) {
                    $hasFk = false;
                }

                if (!$hasFk) {
                    $table->foreign('position_id')->references('id')->on('positions')->nullOnDelete();
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('candidate', function (Blueprint $table) {
            if (Schema::hasColumn('candidate', 'position_id')) {
                try {
                    $table->dropForeign(['position_id']);
                } catch (\Throwable $e) {
                    // ignore
                }
                $table->dropColumn('position_id');
            }
        });
    }
};
