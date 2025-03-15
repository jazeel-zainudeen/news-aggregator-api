<?php

namespace App\Http\Filters;

use Carbon\Carbon;

class NewsArticleFilter extends QueryFilter
{
    /**
     * Filter news articles by search keyword (title, description, or author).
     */
    public function search(?string $searchKeyword): void
    {
        $this->builder->when(! empty($searchKeyword), function ($query) use ($searchKeyword) {
            $formattedKeyword = collect(explode(' ', trim($searchKeyword)))
                ->filter()
                ->map(fn ($word) => "{$word}*")
                ->implode(' ');

            $query->where(function ($query) use ($formattedKeyword) {
                $query->whereRaw('MATCH(news_articles.title, news_articles.description) AGAINST(? IN BOOLEAN MODE)', [$formattedKeyword])
                    ->orWhereHas('author', function ($query) use ($formattedKeyword) {
                        $query->whereRaw('MATCH(name) AGAINST(? IN BOOLEAN MODE)', [$formattedKeyword]);
                    });
            });
        });
    }

    /**
     * Filter news articles by a specific publication date.
     */
    public function date(?string $date): void
    {
        $this->builder->when(! empty($date), function ($query) use ($date) {
            $query->whereDate('news_articles.published_at', Carbon::parse($date)->toDateString());
        });
    }

    /**
     * Filter news articles by category.
     */
    public function category(?int $categoryId): void
    {
        $this->builder->when(! empty($categoryId), function ($query) use ($categoryId) {
            $query->where('news_articles.category_id', $categoryId);
        });
    }

    /**
     * Filter news articles by source.
     */
    public function source(?int $sourceId): void
    {
        $this->builder->when(! empty($sourceId), function ($query) use ($sourceId) {
            $query->where('news_articles.source_id', $sourceId);
        });
    }
}
