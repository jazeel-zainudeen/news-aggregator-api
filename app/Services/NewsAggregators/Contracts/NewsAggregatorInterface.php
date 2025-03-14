<?php

namespace App\Services\NewsAggregators\Contracts;

interface NewsAggregatorInterface
{
    /**
     * Fetch news articles from the news aggregator.
     *
     * @param  array<int|string, mixed>  $data  Data required to fetch news articles.
     * @return array<int, mixed> The fetched news articles.
     */
    public function fetch(array $data): array;
}
