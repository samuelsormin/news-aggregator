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

class SaveNewsApiArticle implements ShouldQueue
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
        // Save authors, sources & articles to db
        try {
            DB::beginTransaction();

            $author = Author::where('name', $this->article->author)->first();
            $source = Source::where('name', $this->article->source->name)->first();

            // Create author if it doesn't exist
            if (empty($author)) {
                $author = Author::create([
                    'name' => $this->article->author
                ]);
            }

            // Create source if it doesn't exist
            if (empty($source)) {
                $source = Source::create([
                    'name' => $this->article->source->name
                ]);
            }

            $data = [
                'title' => $this->article->title,
                'description' => $this->article->description,
                'url' => $this->article->url,
                'thumbnail' => $this->article->urlToImage,
                'published_at' => date('Y-m-d H:i:s', strtotime($this->article->publishedAt)),
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
