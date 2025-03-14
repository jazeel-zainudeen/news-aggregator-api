<?php

use App\Console\Commands\FetchNewsArticles;
use Illuminate\Support\Facades\Schedule;

Schedule::command(FetchNewsArticles::class)->everyThirtyMinutes();
