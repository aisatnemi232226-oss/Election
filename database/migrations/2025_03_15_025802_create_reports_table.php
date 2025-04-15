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
        Schema::create('reports', function (Blueprint $table) {
            $table->bigIncrements('id')->primary();
            $table->string('name'); 
            $table->text('description');
            $table->text('datalisted'); 
            $table->enum('type', [
                'Voting statistics', 
                'Candidate performance', 
                'Election summary', 
                'User participation', 
                'System logs'
            ]); // Added more report types
            $table->unsignedBigInteger('admin_id');
            $table->timestamps(); 
            $table->softDeletes(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
