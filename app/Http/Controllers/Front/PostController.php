<?php

namespace Framework\Http\Controllers\Front;

use Framework\ Models\Tag;
use Framework\ Models\Category;
use Illuminate\Http\Request;
use Framework\ Http\Controllers\Controller;
use Framework\ Http\Requests\SearchRequest;
use Framework\ Repositories\PostRepository;

class PostController extends Controller
{
    /**
     * The PostRepository instance.
     *
     * @var \Framework\Repositories\PostRepository
     */
    protected $postRepository;

    /**
     * The pagination number.
     *
     * @var int
     */
    protected $nbrPages;

    /**
     * Create a new PostController instance.
     *
     * @param  \Framework\Repositories\PostRepository $postRepository
     * @return void
    */
    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
        $this->nbrPages = config('app.nbrPages.front.posts');
    }


    /**
     * Display a listing of the posts for the specified category.
     *
     * @param  \Framework\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function category(Category $category)
    {
        $posts = $this->postRepository->getActiveOrderByDateForCategory($this->nbrPages, $category->slug);
        $info = __('Posts for category: ') . '<strong>' . $category->title . '</strong>';

        return view('front.index', compact('posts', 'info'));
    }

    /**
     * Display the specified post by slug.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  string $slug
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $slug)
    {
        $user = $request->user();

        return view('front.post', array_merge($this->postRepository->getPostBySlug($slug), compact('user')));
    }

    /**
     * Get posts for specified tag
     *
     * @param  \Framework\Models\Tag  $tag
     * @return \Illuminate\Http\Response
     */
    public function tag(Tag $tag)
    {
        $posts = $this->postRepository->getActiveOrderByDateForTag($this->nbrPages, $tag->id);
        $info = __('Posts found with tag ') . '<strong>' . $tag->tag . '</strong>';

        return view('front.index', compact('posts', 'info'));
    }

    /**
     * Get posts with search
     *
     * @param  \Framework\Http\Requests\SearchRequest $request
     * @return \Illuminate\Http\Response
     */
    public function search(SearchRequest $request)
    {
        $search = $request->search;
        $posts = $this->postRepository->search($this->nbrPages, $search)->appends(compact('search'));
        $info = __('Posts found with search: ') . '<strong>' . $search . '</strong>';

        return view('front.index', compact('posts', 'info'));
    }
}
