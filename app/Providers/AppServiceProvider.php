<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Relation::morphMap([
            'category' => 'App\Models\Category',
            'source' => 'App\Models\Source',
            'author' => 'App\Models\Author',
        ]);

        \Illuminate\Support\Facades\DB::listen(function ($query) {
            $channel = \Illuminate\Support\Facades\Log::build([
                'driver' => 'daily',
                'path' => storage_path('logs/queries.log'),
                'days' => 1,
            ]);

            \Illuminate\Support\Facades\Log::stack(['daily' => $channel])
                ->info($query->time . 'ms - ' . vsprintf(str_replace('?', '%s', $query->sql), $query->bindings) . PHP_EOL);
        });
    }
}
