<?php

namespace App\Console\Commands;

use App\Jobs\FetchByCategory;
use App\Models\Article;
use App\Models\Category;
use App\Models\Source;
use \Illuminate\Console\Command;

class FetchNewsApiArticles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetchArticles:newsApi';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch articles from NewsApi';

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
        $sourceNYTimes = Source::where('name', env('NYTIMES_SOURCE'))->first();
        $sourceTheGuardian = Source::where('name', env('THEGUARDIAN_SOURCE'))->first();
        $latestArticle = null;

        // get latest article
        if (!empty($sourceNYTimes) && !empty($sourceTheGuardian)) {
            $latestArticle = Article::where([
                ['source_id', '!=', $sourceNYTimes->id],
                ['source_id', '!=', $sourceTheGuardian->id]
            ])->orderBy('published_at', 'DESC')->first();
            //
        } else if (!empty($sourceNYTimes) && empty($sourceTheGuardian)) {
            $latestArticle = Article::where('source_id', '!=', $sourceNYTimes->id)->orderBy('published_at', 'DESC')->first();
            //
        } else if (empty($sourceNYTimes) && !empty($sourceTheGuardian)) {
            $latestArticle = Article::where('source_id', '!=', $sourceTheGuardian->id)->orderBy('published_at', 'DESC')->first();
        }

        // get the latest article's date
        $latestDate = empty($latestArticle) ? null : date('Y-m-d H:i:s', strtotime($latestArticle->published_at . '+1 seconds'));

        $categories = Category::all();

        foreach ($categories as $category) {
            // invoke job to fetch article by category
            dispatch(new FetchByCategory($category, $latestDate, env('NEWSAPI_SOURCE')))->onQueue('cat_' . $category->name);
        }
    }
}
