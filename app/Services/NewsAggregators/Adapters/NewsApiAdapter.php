<?php

namespace App\Services\NewsAggregators\Adapters;

use App\Services\NewsAggregators\Contracts\NewsAggregatorInterface;
use App\Services\NewsAggregators\Enum\NewsAggregatorTypeEnum;
use App\Services\NewsAggregators\Enum\NewsApiCategoryEnum;
use App\Services\NewsAggregators\Exception\NewsAggregatorException;
use App\Services\NewsAggregators\Repositories\NewsArticleRepository;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NewsApiAdapter implements NewsAggregatorInterface
{
    /**
     * NewsArticleRepository instance.
     */
    protected NewsArticleRepository $newsArticleRepository;

    /**
     * NewsApiAdapter constructor.
     *
     * @param  NewsArticleRepository  $newsArticleRepository
     */
    public function __construct()
    {
        $this->newsArticleRepository = new NewsArticleRepository;
    }

    /**
     * {@inheritDoc}
     */
    public function fetch(array $data): array
    {
        try {
            $apiKey = config('services.news_api.key');
            $apiEndpoint = config('services.news_api.endpoint');

            if (empty($apiKey) || empty($apiEndpoint)) {
                throw NewsAggregatorException::missingConfiguration(NewsAggregatorTypeEnum::NEWS_API);
            }

            $articles = [];

            $totalLimit = $data['limit'] ?? 100;

            $categories = NewsApiCategoryEnum::cases();

            $singleCategoryLimit = (int) floor($totalLimit / count($categories));

            foreach ($categories as $category) {
                $response = Http::withToken($apiKey)
                    ->get($apiEndpoint . 'top-headlines', [
                        'pageSize' => $singleCategoryLimit,
                        'category' => $category->value,
                    ]);

                if ($response->failed() || $response->json('status') !== 'ok') {
                    throw NewsAggregatorException::failedToFetch(NewsAggregatorTypeEnum::NEWS_API);
                }

                $articles = $response->json('articles', []);

                if (! empty($articles)) {
                    DB::transaction(function () use ($articles, $category) {
                        foreach ($articles as $article) {
                            $this->newsArticleRepository->create(array_merge($article, ['category' => $category->value]), NewsAggregatorTypeEnum::NEWS_API);
                        }
                    });
                }
            }

            return $articles;
        } catch (Exception $exception) {
            Log::error('Failed to fetch news articles from NewsAPI', [
                'exception' => $exception,
                'data' => $data,
            ]);

            throw $exception;
        }
    }
}
