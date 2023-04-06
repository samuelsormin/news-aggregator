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

class SaveTheGuardianArticle implements ShouldQueue
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
            !empty($this->article->webTitle)
            && !empty($this->article->fields->trailText)
            && !empty($this->article->webUrl)
            && !empty($this->article->fields->thumbnail)
            && !empty($this->article->webPublicationDate)
            && !empty($this->article->fields->byline)
        ) {
            // Save authors, sources & articles to db
            try {
                DB::beginTransaction();

                $author = Author::where('name', $this->article->fields->byline)->first();
                $source = Source::where('name', env('THEGUARDIAN_SOURCE'))->first();

                // Create author if it doesn't exist
                if (empty($author)) {
                    $author = Author::create([
                        'name' => $this->article->fields->byline
                    ]);
                }

                // Create source if it doesn't exist
                if (empty($source)) {
                    $source = Source::create([
                        'name' => env('THEGUARDIAN_SOURCE')
                    ]);
                }

                $data = [
                    'title' => $this->article->webTitle,
                    'description' => $this->article->fields->trailText,
                    'url' => $this->article->webUrl,
                    'thumbnail' => $this->article->fields->thumbnail,
                    'published_at' => date('Y-m-d H:i:s', strtotime($this->article->webPublicationDate)),
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
