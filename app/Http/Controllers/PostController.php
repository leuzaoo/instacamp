<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    // Here we say that only logged in users can use the functions below
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $posts = Post::with('user')->latest()->get();

        return view('posts.index', compact('posts'));
    }

    public function create()
    {
        return view('posts.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'caption' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $imagePath = $request->file('image')->store('uploads', 'public');

        /** @var User $user */
        $user = $request->user();

        $user->posts()->create([
            'caption' => $data['caption'],
            'image_path' => $imagePath,
        ]);

        return redirect('/profile/' . $user->getKey());
    }

    public function show(Post $post)
    {
        return view('posts.show', compact('post'));
    }

    public function edit(Request $request, Post $post)
    {
        /** @var User $user */
        $user = $request->user();

        if ((string) $user->getKey() !== (string) $post->user_id) {
            abort(403, 'Unauthorized action.');
        }

        return view('posts.edit', compact('post'));
    }

    public function update(Request $request, Post $post)
    {
        /** @var User $user */
        $user = $request->user();

        if ((string) $user->getKey() !== (string) $post->user_id) {
            abort(403, 'Unauthorized action.');
        }

        $data = $request->validate([
            'caption' => 'required',
        ]);

        $post->update($data);

        return redirect('/posts/' . $post->getKey());
    }

    public function destroy(Request $request, Post $post)
    {
        /** @var User $user */
        $user = $request->user();

        if ((string) $user->getKey() !== (string) $post->user_id) {
            abort(403, 'Unauthorized action.');
        }

        Storage::disk('public')->delete($post->image_path);

        $post->delete();

        return redirect('/profile/' . $user->getKey());
    }
}
