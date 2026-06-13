<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Post $post, Request $request)
    {
        /** @var User $user */
        $user = $request->user();

        $post->likes()->firstOrCreate([
            'user_id' => $user->getKey(),
        ]);

        return back();
    }

    public function destroy(Post $post, Request $request)
    {
        /** @var User $user */
        $user = $request->user();

        $post->likes()
            ->where('user_id', $user->getKey())
            ->delete();

        return back();
    }
}
