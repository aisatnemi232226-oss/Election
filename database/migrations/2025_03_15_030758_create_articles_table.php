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
        Schema::create('articles', function (Blueprint $table) {
            $table->bigIncrements('id')->primary();
            $table->string('title');
            $table->enum('type', [
                'Contacts', 
                'Guides', 
                'Announcements', 
                'News', 
                'Updates', 
                'Policies'
            ]); // Added more categories
            $table->longText('content');
            $table->string('author');
            $table->timestamps(); 
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
