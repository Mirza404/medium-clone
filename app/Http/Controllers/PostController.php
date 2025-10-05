<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Http\Requests\PostCreateRequest;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();

        $query =  Post::with(['user', 'media'])
            ->where('published_at', '<=', now())
            ->withCount('claps')
            ->latest();
        if ($user) {
            $ids = $user->following()->pluck('users.id');
            $query->whereIn('user_id', $ids);
        }

        $posts = $query->simplePaginate(5);
        return view('post.index', [
            'posts' => $posts,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::get();
        return view('post.create', ['categories' => $categories]);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PostCreateRequest $request)
    {
        $data = $request->validated();

        // Generate slug from title
        $data['slug'] = Str::slug($data['title']);

        // Ensure the slug is unique
        $data['slug'] = $this->makeUniqueSlug($data['slug']);

        $data['user_id'] = Auth::id();

        $post = Post::create($data);

        $post->refresh(); // Make sure we have the saved model
        $post->addMediaFromRequest('image')->toMediaCollection();

        return redirect()->route('dashboard');
    }

    /**
     * Generate a unique slug by appending a number if necessary.
     *
     * @param string $slug
     * @return string
     */
    private function makeUniqueSlug(string $slug): string
    {
        $originalSlug = $slug;
        $count = 1;

        // Check if the slug already exists in the database
        while (Post::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        return $slug;
    }

    /**
     * Display the specified resource.
     */
     public function show(string $username, Post $post)
    {
        return view('post.show', [
            'post' => $post,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        //
    }

    public function category(Category $category)
    {
        $posts = $category
        ->posts()
        ->with(['user', 'media'])
        ->withCount('claps')
        ->latest()->simplePaginate(5);
        return view('post.index', [
            'posts' => $posts,
        ]);
    }
}
