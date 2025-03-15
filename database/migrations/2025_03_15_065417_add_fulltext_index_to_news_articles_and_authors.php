<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('news_articles', function (Blueprint $table) {
            $table->fullText(['title', 'description']);
        });

        Schema::table('authors', function (Blueprint $table) {
            $table->fullText(['name']);
        });
    }

    public function down(): void
    {
        Schema::table('news_articles', function (Blueprint $table) {
            $table->dropFullText(['title', 'description']);
        });

        Schema::table('authors', function (Blueprint $table) {
            $table->dropFullText(['name']);
        });
    }
};
