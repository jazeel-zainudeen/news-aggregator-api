<?php

namespace App\Services\NewsAggregators\Repositories;

use App\Models\NewsArticle;

class NewsArticleRepository
{
    /**
     * Save news article to the database.
     *
     * @param  array<int, mixed>  $attributes  The attributes to store.
     */
    public function create(array $attributes): NewsArticle
    {
        $model = NewsArticle::updateOrCreate(
            [
                'category' => $attributes['category'],
                'author' => $attributes['author'],
                'title' => $attributes['title'],
                'source' => $attributes['source'],
                'published_at' => $attributes['published_at'],
            ],
            $attributes
        );

        return $model;
    }
}
