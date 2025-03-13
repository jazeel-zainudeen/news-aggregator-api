<?php

namespace App\Console\Commands;

use App\Services\NewsAggregators\Enum\NewsAggregatorTypeEnum;
use App\Services\NewsAggregators\NewsAggregatorService;
use Exception;
use Illuminate\Console\Command;

class FetchNewsArticles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'news:fetch {--limit=100 : Number of articles to fetch per aggregator}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch and update articles from third-party news sources';

    /**
     * News aggregator service instance.
     */
    protected NewsAggregatorService $newsAggregatorService;

    /**
     * Create a new command instance.
     */
    public function __construct(NewsAggregatorService $newsAggregatorService)
    {
        parent::__construct();
        $this->newsAggregatorService = $newsAggregatorService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $limit = (int) $this->option('limit');
        if ($limit <= 0) {
            $this->error('Invalid limit provided. It must be a positive number.');

            return;
        }

        $this->info("Starting news fetching process with a limit of {$limit} articles per aggregator...");

        try {
            $aggregators = NewsAggregatorTypeEnum::cases();
            foreach ($aggregators as $aggregator) {
                $aggregatorTitle = NewsAggregatorTypeEnum::getTitle($aggregator);

                $this->newLine();
                $this->info("Fetching articles from: {$aggregatorTitle}");

                try {
                    $articles = $this->newsAggregatorService->fetchNewsArticles($aggregator->value, ['limit' => $limit]);
                    $count = count($articles);
                    $this->info("✔ Successfully fetched {$count} articles from {$aggregatorTitle}");
                } catch (Exception $exception) {
                    $this->error("❌ Failed to fetch from {$aggregatorTitle}: " . $exception->getMessage());
                }
            }

            $this->newLine();
            $this->info('✅ News fetching process completed successfully.');
        } catch (Exception $e) {
            $this->error('❌ Unexpected error occurred: ' . $e->getMessage());
        }
    }
}
