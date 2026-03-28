<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\ReadingList;

class ReadingListPostController extends Controller
{
    /**
     * Toggle the presence of a post inside a reading list.
     */
    public function toggle(ReadingList $readingList, Post $post)
    {
        abort_unless(auth()->check(), 401);
        abort_if($readingList->user_id !== auth()->id(), 403);

        $isAttached = $readingList->posts()
            ->where('post_id', $post->id)
            ->exists();

        if ($isAttached) {
            $readingList->posts()->detach($post);
        } else {
            $readingList->posts()->attach($post);
        }

        return response()->json([
            'attached' => ! $isAttached,
            'readingListId' => $readingList->id,
        ]);
    }
}
