<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BlogPost;
class BlogController extends Controller
{
public function index()
{
    $posts = BlogPost::latest()->paginate(6);
    return view('blog.index', compact('posts'));
}

public function show($slug)
{
    $post = BlogPost::where('slug', $slug)->firstOrFail();
    $linked = BlogPost::whereIn('id', $post->linked_articles ?? [])->get();
    return view('blog.show', compact('post', 'linked'));
}

}
