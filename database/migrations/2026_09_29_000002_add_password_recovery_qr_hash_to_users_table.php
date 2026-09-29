<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'password_recovery_qr_hash')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->string('password_recovery_qr_hash')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'password_recovery_qr_hash')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->dropColumn('password_recovery_qr_hash');
            });
        }
    }
};