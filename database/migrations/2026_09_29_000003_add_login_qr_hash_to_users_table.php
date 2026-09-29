<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'login_qr_hash')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->string('login_qr_hash')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'login_qr_hash')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->dropColumn('login_qr_hash');
            });
        }
    }
};