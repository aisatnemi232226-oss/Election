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
        Schema::create('results', function (Blueprint $table) {
            $table->bigIncrements('id')->primary();
            $table->unsignedBigInteger('election_id')->nullable();
            $table->unsignedBigInteger('option_id')->nullable();
            $table->integer('totalVotes')->nullable();
            $table->integer('totalVoters')->nullable();
            $table->timestamp('created_at')->useCurrentOnUpdate()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('results');
    }
};
