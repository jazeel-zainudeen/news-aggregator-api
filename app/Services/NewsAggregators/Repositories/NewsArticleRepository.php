<?php

namespace App\Services\NewsAggregators\Repositories;

use App\Models\Author;
use App\Models\Category;
use App\Models\NewsArticle;
use App\Models\Source;
use App\Services\NewsAggregators\Enum\NewsAggregatorTypeEnum;
use App\Services\NewsAggregators\Exception\NewsAggregatorException;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class NewsArticleRepository
{
    /**
     * Save news article to the database.
     */
    public function create(array $attributes, NewsAggregatorTypeEnum $newsAggregatorType): void
    {
        $method = match ($newsAggregatorType) {
            NewsAggregatorTypeEnum::NEWS_API => $this->createNewsApiArticle($attributes),
            NewsAggregatorTypeEnum::THE_GUARDIAN => $this->createTheGuardianArticle($attributes),
            NewsAggregatorTypeEnum::NEW_YORK_TIMES => $this->createNewYorkTimesArticle($attributes),
            default => throw NewsAggregatorException::invalidAggregatorType(),
        };
    }

    /**
     * Create a NewsArticle model.
     */
    protected function createNewsApiArticle(array $attributes): void
    {
        $attributes['source'] = $attributes['source']['name'] ?? null;
        $attributes['publishedAt'] = Carbon::parse($attributes['publishedAt'] ?? now());
        $attributes['api_source'] = NewsAggregatorTypeEnum::NEWS_API->value;
        
        $this->storeArticle($attributes);
    }

    protected function createTheGuardianArticle(array $attributes): void
    {
        $attributes['category'] = !empty($attributes['sectionName']) ? Str::slug($attributes['sectionName']) : null;
        $attributes['author'] = $attributes['fields']['byline'] ?? null;
        $attributes['source'] = 'The Guardian';
        $attributes['title'] = $attributes['webTitle'] ?? null;
        $attributes['description'] = $attributes['fields']['trailText'] ?? null;
        $attributes['publishedAt'] = Carbon::parse($attributes['webPublicationDate'] ?? now());
        $attributes['urlToImage'] = $attributes['fields']['thumbnail'] ?? null;
        $attributes['content'] = $attributes['fields']['bodyText'] ?? null;
        $attributes['api_source'] = NewsAggregatorTypeEnum::THE_GUARDIAN->value;
        
        $this->storeArticle($attributes);
    }

    protected function createNewYorkTimesArticle(array $attributes): void
    {
        $attributes['category'] = $attributes['subsection'] ?? $attributes['section'] ?? null;
        $attributes['author'] = $attributes['byline'] ?? null;
        $attributes['source'] = 'The New York Times';
        $attributes['publishedAt'] = Carbon::parse($attributes['published_date'] ?? now());
        $attributes['urlToImage'] = Arr::get($attributes, 'multimedia.0.url');
        $attributes['content'] = $attributes['abstract'] ?? null;
        $attributes['api_source'] = NewsAggregatorTypeEnum::NEW_YORK_TIMES->value;
        
        $this->storeArticle($attributes);
    }

    /**
     * Store article in the database.
     */
    private function storeArticle(array $attributes): void
    {
        $values = [
            'category_id' => $this->getCategory($attributes['category'] ?? null)?->id,
            'author_id' => $this->getAuthor($attributes['author'] ?? null)?->id,
            'source_id' => $this->getSource($attributes['source'] ?? null)?->id,
            'title' => $attributes['title'] ?? null,
            'description' => $attributes['description'] ?? null,
            'published_at' => $attributes['publishedAt'],
            'url_to_image' => $attributes['urlToImage'] ?? null,
            'content' => $attributes['content'] ?? null,
            'api_source' => $attributes['api_source'],
        ];

        NewsArticle::updateOrCreate(
            Arr::only($values, ['category_id', 'author_id', 'source_id', 'title', 'published_at']),
            $values
        );
    }

    /**
     * Get or create a source.
     */
    private function getSource(?string $source): ?Source
    {
        return $this->cacheEntity(Source::class, $source);
    }

    /**
     * Get or create a category.
     */
    private function getCategory(?string $category): ?Category
    {
        return $this->cacheEntity(Category::class, $category);
    }

    /**
     * Get or create an author.
     */
    private function getAuthor(?string $author): ?Author
    {
        if ($author) {
            $author = Str::of($author)->after('By ');
        }
        return $this->cacheEntity(Author::class, $author);
    }

    /**
     * Cache and retrieve entity.
     */
    private function cacheEntity(string $model, ?string $key): ?object
    {
        if (empty($key)) {
            return null;
        }

        return Cache::remember("{$model}_{$key}", now()->addDay(), function () use ($model, $key) {
            return $model::firstOrCreate(['name' => $key]);
        });
    }
}
