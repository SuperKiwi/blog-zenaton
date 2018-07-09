<?php

namespace Framework\Http\Controllers\Back;

use Framework\ Models\Post;
use Framework\ Models\Category;
use Framework\ Http\Requests\PostRequest;
use Framework\ Http\Controllers\Controller;
use Framework\ Repositories\PostRepository;
use Framework\Zenaton\Events\PostModeratedEvent;
use Framework\Zenaton\Workflows\PostModerationWorkflow;

class PostController extends Controller
{
    use Indexable;

    /**
     * Create a new PostController instance.
     *
     * @param  \Framework\Repositories\PostRepository $repository
     */
    public function __construct(PostRepository $repository)
    {
        $this->repository = $repository;

        $this->table = 'posts';
    }

    /**
     * Update "new" field for post.
     *
     * @param  \Framework\Models\Post $post
     * @return \Illuminate\Http\Response
     */
    public function updateSeen(Post $post)
    {
        $post->ingoing->delete();

        return response()->json();
    }

    /**
     * Update "active" field for post.
     *
     * @param  \Framework\Models\Post $post
     * @param  bool $status
     * @return \Illuminate\Http\Response
     */
    public function updateActive(Post $post, $status = false)
    {
        $post->active = $status;
        $post->save();

        return response()->json();
    }

    /**
     * Show the form for creating a new post.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::all()->pluck('title', 'id');

        return view('back.posts.create', compact('categories'));
    }

    /**
     * Store a newly created post in storage.
     *
     * @param  \Framework\Http\Requests\PostRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PostRequest $request)
    {
        $this->repository->store($request);

        return redirect(route('posts.index'))->with('post-ok', __('The post has been successfully created'));
    }

    /**
     * Display the post.
     *
     * @param  \Framework\Models\Post $post
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        return view('back.posts.show', compact('post'));
    }

    /**
     * Show the form for editing the post.
     *
     * @param  \Framework\Models\Post $post
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        $this->authorize('manage', $post);

        $categories = Category::all()->pluck('title', 'id');

        return view('back.posts.edit', compact('post', 'categories'));
    }

    /**
     * Update the post in storage.
     *
     * @param  \Framework\Http\Requests\PostRequest  $request
     * @param  \Framework\Models\Post $post
     * @return \Illuminate\Http\Response
     */
    public function update(PostRequest $request, Post $post)
    {
        $this->authorize('manage', $post);

        $this->repository->update($post, $request);

        return back()->with('post-ok', __('The post has been successfully updated'));
    }

    /**
     * Remove the post from storage.
     *
     * @param Post $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        $this->authorize('manage', $post);

        $post->delete();

        return response()->json();
    }

    public function accept(Post $post)
    {
        $this->moderate($post, true);

        redirect(route('posts.index'))->with('post-ok', __('The post has been successfully accepted'));
    }

    public function refuse(Post $post)
    {
        $this->moderate($post, false);

        redirect(route('posts.index'))->with('post-ok', __('The post has been successfully refused'));
    }

    protected function moderate(Post $post, bool $accept)
    {
        $post->moderated = true;
        $post->active = $accept;
        $post->save();

        $event = new PostModeratedEvent($accept);
        PostModerationWorkflow::whereId($post->id)->send($event);
    }
}
