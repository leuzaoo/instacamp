<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request, Post $post)
    {
        $data = $request->validate([
            'comment' => 'required|max:255',
        ]);

        /** @var User $authUser */
        $authUser = $request->user();

        $post->comments()->create([
            'comment' => $data['comment'],
            'user_id' => $authUser->getKey(),
        ]);

        return redirect('/posts/' . $post->getKey());
    }

    public function destroy(Request $request, Comment $comment)
    {
        /** @var User $authUser */
        $authUser = $request->user();

        // Check if the authenticated user is the comment owner or post owner
        if (
            (string) $authUser->getKey() !== (string) $comment->user_id &&
            (string) $authUser->getKey() !== (string) $comment->post->user_id
        ) {
            abort(403, 'Unauthorized action.');
        }

        $postId = $comment->post_id;

        $comment->delete();

        return redirect('/posts/' . $postId);
    }
}
