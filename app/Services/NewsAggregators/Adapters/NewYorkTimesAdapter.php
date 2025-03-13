<?php

namespace App\Services\NewsAggregators\Adapters;

use App\Services\NewsAggregators\Contracts\NewsAggregatorInterface;

class NewYorkTimesAdapter implements NewsAggregatorInterface
{
    /**
     * Fetch news articles from New York Times.
     *
     * @param array<int|string, mixed> $data Data required to fetch news articles.
     * @return array<int, mixed> The fetched news articles.
     */
    public function fetch(array $data): array
    {
        // Fetch news articles from new york times API
        return [];
    }
}