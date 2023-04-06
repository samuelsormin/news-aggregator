<?php

namespace App\Console\Commands;

use App\Jobs\FetchByCategory;
use App\Models\Article;
use App\Models\Category;
use App\Models\Source;
use \Illuminate\Console\Command;

class FetchNYTimesArticle extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetchArticles:nyTimes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch articles from New York Times';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): void
    {
        $source = Source::where('name', env('NYTIMES_SOURCE'))->first();
        $latestDate = null;

        // get the latest article's date
        if (!empty($source)) {
            $latestArticle = Article::where('source_id', $source->id)->orderBy('published_at', 'DESC')->first();
            $latestDate = empty($latestArticle) ? null : date('Y-m-d H:i:s', strtotime($latestArticle->published_at . '+1 seconds'));
        }

        $categories = Category::all();

        foreach ($categories as $category) {
            // invoke job to fetch article by category
            dispatch(new FetchByCategory($category, $latestDate, env('NYTIMES_SOURCE')))->onQueue('cat_' . $category->name);
        }
    }
}
