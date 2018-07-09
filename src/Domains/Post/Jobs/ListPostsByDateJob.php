<?php

namespace App\Domains\Post\Jobs;

use App\Data\Post;
use Lucid\Foundation\Job;

class ListPostsByDateJob extends Job
{
    private $limit;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($limit)
    {
        $this->limit = $limit;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(Post $post)
    {
        return $post->select('id', 'title', 'slug', 'excerpt', 'image')
            ->whereActive(true)
            ->latest()
            ->paginate($this->limit);
    }
}
