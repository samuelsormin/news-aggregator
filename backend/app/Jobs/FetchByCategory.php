<?php

namespace App\Jobs;

use App\Helpers\NewsApi;
use App\Helpers\NYTimesApi;
use App\Helpers\TheGuardianApi;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FetchByCategory implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $category;
    protected $latestDate;
    protected $source;

    /**
     * Create a new job instance.
     * 
     * @param object $category
     * @param string $latestDate
     * @param integer $source
     */
    public function __construct($category, $latestDate, $source)
    {
        $this->category = $category;
        $this->latestDate = $latestDate;
        $this->source = $source;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        switch ($this->source) {
            case env('NEWSAPI_SOURCE'):
                $from = empty($this->latestDate) ? "" : "&from={$this->latestDate}";
                $articles = NewsApi::request("everything?excludeDomains=theguardian.com&q={$this->category->name}{$from}&pageSize=10");

                if (!empty($articles)) {
                    foreach ($articles->articles as $article) {
                        dispatch(new SaveNewsApiArticle($article, $this->category->id))->onQueue('saveNewsApiArticle');
                    }
                }
                break;

            case env('NYTIMES_SOURCE'):
                $articles = NYTimesApi::request("articlesearch.json?q={$this->category->name}&sort=newest");
                $latestDate = empty($this->latestDate) ? '1900-01-01 00:00:01' : $this->latestDate;

                if (!empty($articles->response)) {
                    foreach ($articles->response->docs as $article) {
                        // invoke job to save NYTimes's article
                        if (strtotime($article->pub_date) > strtotime($latestDate)) {
                            dispatch(new SaveNYTimesArticle($article, $this->category->id, $this->latestDate))->onQueue('saveNYTimesArticle');
                        }
                    }
                }
                break;

            case env('THEGUARDIAN_SOURCE'):
                $articles = TheGuardianApi::request("search?q={$this->category->name}&show-fields=trailText,thumbnail,byline&order-by=newest");
                $latestDate = empty($this->latestDate) ? '1900-01-01 00:00:01' : $this->latestDate;

                if (!empty($articles->response)) {
                    foreach ($articles->response->results as $article) {
                        // invoke job to save The Guardian's article
                        if (strtotime($article->webPublicationDate) > strtotime($latestDate)) {
                            dispatch(new SaveTheGuardianArticle($article, $this->category->id, $this->latestDate))->onQueue('saveTheGuardianArticle');
                        }
                    }
                }
                break;
        }
    }
};
