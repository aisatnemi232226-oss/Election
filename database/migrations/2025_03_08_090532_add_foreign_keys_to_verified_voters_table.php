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
        Schema::table('verified_voters', function (Blueprint $table) {
            // Add foreign key constraints
            $table->foreign('users_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('acadyr_id')->references('id')->on('acadyr')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('sem_id')->references('id')->on('semesters')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('yrlvl_id')->references('id')->on('yearlevel')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('admin_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('verified_voters', function (Blueprint $table) {
            $table->dropForeign(['users_id']);
            $table->dropForeign(['acadyr_id']);
            $table->dropForeign(['sem_id']);
            $table->dropForeign(['course_id']);
            $table->dropForeign(['yrlvl_id']);
            $table->dropForeign(['admin_id']);
        });
    }
};