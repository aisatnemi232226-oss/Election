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
        Schema::create('candidate', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id')->primary();
            $table->string('name'); 
            $table->string('id_number')->unique();
            $table->unsignedBigInteger('electionparty_id');
            $table->unsignedBigInteger('course_id');
            $table->text('platforms'); 
            $table->text('description');
            $table->string('status');
            $table->string('profile_img');
            $table->string('banner_img'); 
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidate');
    }
};
