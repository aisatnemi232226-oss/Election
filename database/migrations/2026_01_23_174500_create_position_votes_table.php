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
        if (!Schema::hasTable('position_votes')) {
            Schema::create('position_votes', function (Blueprint $table) {
                $table->bigIncrements('id');

                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('position_id');
                $table->unsignedBigInteger('candidate_id');

                $table->timestamps();

                $table->unique(['user_id', 'position_id'], 'position_votes_user_position_unique');

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('position_id')->references('id')->on('positions')->onDelete('cascade');
                $table->foreign('candidate_id')->references('id')->on('candidate')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('position_votes');
    }
};
