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
        Schema::create('verified_voters', function (Blueprint $table) {
            $table->bigIncrements('id')->primary();
            $table->unsignedBigInteger('importgroup')->nullable();
            $table->unsignedBigInteger('users_id')->nullable()->index('users_id');
            $table->unsignedBigInteger('acadyr_id')->nullable()->index('acadyr_id');
            $table->unsignedBigInteger('sem_id')->nullable()->index('sem_id');
            $table->unsignedBigInteger('course_id')->nullable()->index('course_id');
            $table->unsignedBigInteger('yrlvl_id')->nullable()->index('yrlvl_id');
            $table->unsignedBigInteger('admin_id')->nullable()->index('admin_id');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('verified_voters');
    }
};
