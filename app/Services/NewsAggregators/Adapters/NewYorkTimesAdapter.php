<?php

namespace App\Services\NewsAggregators\Adapters;

use App\Services\NewsAggregators\Contracts\NewsAggregatorInterface;
use App\Services\NewsAggregators\Enum\NewsAggregatorTypeEnum;
use App\Services\NewsAggregators\Exception\NewsAggregatorException;
use App\Services\NewsAggregators\Repositories\NewsArticleRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class NewYorkTimesAdapter implements NewsAggregatorInterface
{
    /**
     * NewsArticleRepository instance.
     */
    protected NewsArticleRepository $newsArticleRepository;

    /**
     * TheGuardianAdapter constructor.
     *
     * @param  NewsArticleRepository  $newsArticleRepository
     */
    public function __construct()
    {
        $this->newsArticleRepository = new NewsArticleRepository;
    }

    /**
     * Fetch news articles from New York Times.
     *
     * @param  array<int|string, mixed>  $data  Data required to fetch news articles.
     * @return array<int, mixed> The fetched news articles.
     */
    public function fetch(array $data): array
    {
        try {
            $apiKey = config('services.the_new_york_times.key');
            $apiEndpoint = config('services.the_new_york_times.endpoint');

            if (empty($apiKey) || empty($apiEndpoint)) {
                throw NewsAggregatorException::missingConfiguration(NewsAggregatorTypeEnum::NEWS_API);
            }

            $totalLimit = $data['limit'] ?? 100;

            $response = Http::get($apiEndpoint . 'topstories/v2/home.json', [
                'api-key' => $apiKey,
            ]);

            if ($response->failed() || $response->json('status') !== 'OK') {
                throw NewsAggregatorException::failedToFetch(NewsAggregatorTypeEnum::NEWS_API);
            }

            $articles = $response->json('results', []);

            foreach ($articles as $article) {
                $this->newsArticleRepository->create([
                    'source' => 'the-new-york-times',
                    'category' => $this->getCategory($article),
                    'author' => ! empty($article['byline']) ? Str::of($article['byline'])->after('By ') : null,
                    'title' => $article['title'] ?? null,
                    'description' => $article['abstract'] ?? null,
                    'published_at' => ! empty($article['published_date']) ? Carbon::parse($article['published_date']) : now(),
                    'url_to_image' => Arr::get($article, 'multimedia.0.url'),
                    'content' => $article['content'] ?? null,
                    'api_source' => NewsAggregatorTypeEnum::NEW_YORK_TIMES->value,
                ]);
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

    /**
     * Get the categories of the article.
     *
     * @param  array<int|string, mixed>  $article  The article data.
     * @return string|null The categories of the article.
     */
    private function getCategory(array $article): ?string
    {
        return ! empty($article['subsection']) ? $article['subsection'] : (! empty($article['section']) ? $article['section'] : null);
    }
}
