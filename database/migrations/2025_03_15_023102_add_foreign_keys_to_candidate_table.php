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
        Schema::table('candidate', function (Blueprint $table) {
            $table->foreign('course_id')->references('id')->on('courses')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('electionparty_id')->references('id')->on('electionparty')->onDelete('restrict')->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('candidate', function (Blueprint $table) {
            $table->dropForeign(['course_id']);
            $table->dropForeign(['electionparty_id']);
        });
    }
};
