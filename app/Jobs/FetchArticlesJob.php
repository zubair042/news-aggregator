<?php

namespace App\Jobs;

use App\Services\ArticleService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class FetchArticlesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(ArticleService $articleService): void
    {
        Log::info('FetchArticlesJob started...');
        $articleService->fetchAndStore();
        Log::info('FetchArticlesJob completed successfully.');
    }
}
