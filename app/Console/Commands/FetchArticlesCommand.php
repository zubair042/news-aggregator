<?php

namespace App\Console\Commands;

use App\Jobs\FetchArticlesJob;
use Illuminate\Console\Command;

class FetchArticlesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'articles:fetch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch a job to fetch latest news articles from APIs';

    /**
     * @return void
     */
    public function handle(): void
    {
        $this->info('Dispatching FetchNewsJob to queue...');
        FetchArticlesJob::dispatch();
        $this->info('Job dispatched successfully!');
    }
}
