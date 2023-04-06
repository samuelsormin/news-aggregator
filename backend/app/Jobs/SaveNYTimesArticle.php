<?php

namespace App\Jobs;

use App\Models\Article;
use App\Models\Author;
use App\Models\Source;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SaveNYTimesArticle implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $article;
    protected $categoryId;

    /**
     * Create a new job instance.
     * 
     * @param object $article
     * @param integer $categoryId
     */
    public function __construct($article, $categoryId)
    {
        $this->article = $article;
        $this->categoryId = $categoryId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (
            !empty($this->article->headline->print_headline)
            && !empty($this->article->abstract)
            && !empty($this->article->web_url)
            && !empty($this->article->multimedia[0]->url)
            && !empty($this->article->pub_date)
            && !empty($this->article->byline->original)
        ) {
            // Save authors, sources & articles to db
            try {
                DB::beginTransaction();

                $articleAuthorName = substr($this->article->byline->original, 3);

                $author = Author::where('name', $articleAuthorName)->first();
                $source = Source::where('name', env('NYTIMES_SOURCE'))->first();

                // Create author if it doesn't exist
                if (empty($author)) {
                    $author = Author::create([
                        'name' => $articleAuthorName
                    ]);
                }

                // Create source if it doesn't exist
                if (empty($source)) {
                    $source = Source::create([
                        'name' => env('NYTIMES_SOURCE')
                    ]);
                }

                $data = [
                    'title' => $this->article->headline->print_headline,
                    'description' => $this->article->abstract,
                    'url' => $this->article->web_url,
                    'thumbnail' => sprintf("https://www.nytimes.com/%s", $this->article->multimedia[0]->url),
                    'published_at' => date('Y-m-d H:i:s', strtotime($this->article->pub_date)),
                    'author_id' => $author->id,
                    'category_id' => $this->categoryId,
                    'source_id' => $source->id,
                ];

                Article::create($data);

                DB::commit();
                //
            } catch (\Throwable $th) {
                DB::rollBack();

                Log::error('Create article failed.', [
                    'message' => $th->getMessage(),
                    'trace' => $th->getTraceAsString()
                ]);
            }
        }
    }
}
