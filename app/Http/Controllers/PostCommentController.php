<?php

namespace App\Http\Controllers;

use App\Http\Resources\CommentCollection;
use App\Post;

class PostCommentController extends Controller
{
    public function store(Post $post)
    {
        $data = request()->validate([
            'body' => 'required',
        ]);

        $post->comments()->create(array_merge($data, [
            'user_id' => auth()->user()->id,
        ]));

        return new CommentCollection($post->comments);
    }
}
