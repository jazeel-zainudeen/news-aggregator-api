<?php

use App\Services\NewsAggregators\Enum\NewsAggregatorTypeEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('news_articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('source_id')->nullable()->constrained();
            $table->foreignId('category_id')->nullable()->constrained();
            $table->foreignId('author_id')->nullable()->constrained();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->timestamp('published_at');
            $table->text('url_to_image')->nullable();
            $table->text('content')->nullable();
            $table->enum('api_source', array_map(fn ($case) => $case->value, NewsAggregatorTypeEnum::cases()))
                ->default(NewsAggregatorTypeEnum::NEWS_API->value);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('news_articles');
    }
};
