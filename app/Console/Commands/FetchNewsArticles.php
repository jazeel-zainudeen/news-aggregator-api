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
    protected $signature = 'news:fetch {--limit=100}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch and update articles from third-party news sources';

    /**
     * News aggregator service instance.
     *
     * @var NewsAggregatorService
     */
    protected $newsAggregatorService;

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
    public function handle()
    {
        if (empty($this->option('limit'))) {
            $this->error('Limit option is required.');

            return;
        }

        $this->info('Fetching news articles from all aggregators...');

        try {
            $aggregators = NewsAggregatorTypeEnum::cases();
            foreach ($aggregators as $aggregator) {
                $this->newLine();
                $this->info('Fetching from: ' . $aggregator->name);
                try {
                    $this->newsAggregatorService->fetchNewsArticles($aggregator->value, [
                        'limit' => $this->option('limit'),
                    ]);
                    $this->info('Completed fetching from: ' . $aggregator->name);
                } catch (Exception $exception) {
                    $this->error('Error fetching articles from ' . $aggregator->name . ': ' . $exception->getMessage());
                }
            }
            $this->newLine();
            $this->info('News fetching completed successfully.');
        } catch (Exception $e) {
            $this->error('Error fetching articles: ' . $e->getMessage());
        }
    }
}
