<?php

namespace App\Services\Web\Features;

use Illuminate\Http\Request;
use Lucid\Foundation\Feature;
use App\Domains\Http\Jobs\RespondWithJsonJob;
use App\Domains\Http\Jobs\RespondWithViewJob;
use App\Domains\Post\Jobs\ListPostsByDateJob;

class ListPostsFeature extends Feature
{
    public function handle(Request $request)
    {
        $posts = $this->run(new ListPostsByDateJob(25));  // config('app.nbrPages.front.posts')

        return $this->run(new RespondWithViewJob('web::index', compact('posts')));
    }
}
