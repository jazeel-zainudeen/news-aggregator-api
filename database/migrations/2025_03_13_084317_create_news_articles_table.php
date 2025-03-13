<?php

use App\Services\NewsAggregators\Enum\NewsAggregatorTypeEnum;
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
        Schema::create('news_articles', function (Blueprint $table) {
            $table->id();
            $table->string('source')->nullable();
            $table->string('category')->nullable();
            $table->string('author')->nullable();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->timestamp('published_at');
            $table->text('url_to_image')->nullable();
            $table->text('content')->nullable();
            $table->enum('api_source', array_map(fn($case) => $case->value, NewsAggregatorTypeEnum::cases()))
                ->default(NewsAggregatorTypeEnum::NEWS_API->value);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news_articles');
    }
};
