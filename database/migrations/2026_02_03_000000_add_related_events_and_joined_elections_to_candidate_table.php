<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('candidate', function (Blueprint $table) {
            if (!Schema::hasColumn('candidate', 'related_events')) {
                $table->text('related_events')->nullable()->after('platforms');
            }

            if (!Schema::hasColumn('candidate', 'joined_elections')) {
                $table->longText('joined_elections')->nullable()->after('related_events');
            }
        });
    }

    public function down(): void
    {
        Schema::table('candidate', function (Blueprint $table) {
            if (Schema::hasColumn('candidate', 'joined_elections')) {
                $table->dropColumn('joined_elections');
            }

            if (Schema::hasColumn('candidate', 'related_events')) {
                $table->dropColumn('related_events');
            }
        });
    }
};
