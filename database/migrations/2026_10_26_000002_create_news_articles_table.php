<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('news_articles', function (Blueprint $table) {
            $table->id();
            $table->string('source_name', 120);
            $table->string('title', 500);
            $table->text('summary')->nullable();
            $table->string('url', 1000)->unique();
            $table->timestampTz('published_at')->index();
            $table->json('matched_keywords');
            $table->timestampsTz();
            $table->index(['published_at', 'source_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('news_articles');
    }
};
