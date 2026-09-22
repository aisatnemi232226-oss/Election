<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'course')) {
                $table->string('course')->nullable()->after('idnumber');
            }

            if (!Schema::hasColumn('users', 'year_level')) {
                $table->string('year_level')->nullable()->after('course');
            }

            if (!Schema::hasColumn('users', 'status')) {
                $table->string('status')->nullable()->after('year_level');
            }

            if (!Schema::hasColumn('users', 'admin_role')) {
                $table->string('admin_role')->nullable()->after('status');
            }

            if (!Schema::hasColumn('users', 'profile_image')) {
                $table->string('profile_image')->nullable()->after('admin_role');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'profile_image')) {
                $table->dropColumn('profile_image');
            }

            if (Schema::hasColumn('users', 'admin_role')) {
                $table->dropColumn('admin_role');
            }

            if (Schema::hasColumn('users', 'status')) {
                $table->dropColumn('status');
            }

            if (Schema::hasColumn('users', 'year_level')) {
                $table->dropColumn('year_level');
            }

            if (Schema::hasColumn('users', 'course')) {
                $table->dropColumn('course');
            }
        });
    }
};
