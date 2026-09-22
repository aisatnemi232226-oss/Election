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
            if (!Schema::hasColumn('candidate', 'users_id')) {
                $table->bigInteger('users_id')->nullable()->after('id_number');
            }
        });

        Schema::table('candidate', function (Blueprint $table) {
            if (Schema::hasColumn('candidate', 'users_id')) {
                try {
                    DB::statement('ALTER TABLE `candidate` MODIFY `users_id` bigint(20) NULL');
                } catch (\Throwable $e) {
                    // ignore
                }

                $hasFk = false;

                try {
                    $dbName = DB::getDatabaseName();
                    $rows = DB::select(
                        "SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'candidate' AND COLUMN_NAME = 'users_id' AND REFERENCED_TABLE_NAME IS NOT NULL",
                        [$dbName]
                    );
                    $hasFk = count($rows) > 0;
                } catch (\Throwable $e) {
                    $hasFk = false;
                }

                if (!$hasFk) {
                    $table->foreign('users_id')->references('id')->on('users')->nullOnDelete();
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('candidate', function (Blueprint $table) {
            if (Schema::hasColumn('candidate', 'users_id')) {
                try {
                    $table->dropForeign(['users_id']);
                } catch (\Throwable $e) {
                    // ignore
                }

                $table->dropColumn('users_id');
            }
        });
    }
};
