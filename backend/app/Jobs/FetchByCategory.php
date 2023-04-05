<?php

namespace App\Jobs;

use App\Helpers\NewsApi;
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
    protected $sourceId;

    /**
     * Create a new job instance.
     * 
     * @param object $category
     * @param string $latestDate
     * @param integer $sourceId
     */
    public function __construct($category, $latestDate, $sourceId)
    {
        $this->category = $category;
        $this->latestDate = $latestDate;
        $this->sourceId = $sourceId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $from = empty($this->latestDate) ? "" : "&from={$this->latestDate}";

        // fetch article from the latest date if $latestDate doesn't empty
        switch ($this->sourceId) {
            case env('NEWSAPI_SOURCEID'):
                $articles = NewsApi::request("everything?q={$this->category->name}{$from}&pageSize=10");
                break;

            default:
                $articles = [];
                break;
        }

        foreach ($articles->articles as $article) {
            // invoke job to save NewsApi's article
            if ($this->sourceId == env('NEWSAPI_SOURCEID')) dispatch(new SaveNewsApiArticle($article, $this->category->id))->onQueue('saveNewsApiArticle');
        }
    }
};
