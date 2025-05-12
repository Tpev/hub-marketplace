<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BlogPost;

class BlogPostController extends Controller
{
    public function index()
    {
        $posts = BlogPost::latest()->paginate(10);
        return view('admin.blog.index', compact('posts'));
    }

    public function create()
    {
        $allPosts = BlogPost::all();
        return view('admin.blog.create', compact('allPosts'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|unique:blog_posts,slug',
            'content' => 'required|string',
            'meta_description' => 'nullable|string|max:255',
            'linked_articles' => 'nullable|array',
            'cover_image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('blog_covers', 'public');
        }

        $post = BlogPost::create($data);

        if (!empty($data['linked_articles'])) {
            $post->linkedArticles()->sync($data['linked_articles']);
        }

        return redirect()->route('admin.blog.index')->with('success', 'Blog post created.');
    }

public function edit(BlogPost $blog)
{
    $allPosts = BlogPost::where('id', '!=', $blog->id)->get(); // exclude self
    $linkedArticleIds = $blog->linkedArticles()->pluck('blog_posts.id')->toArray();

    return view('admin.blog.edit', [
        'blogPost' => $blog,            // this will be used in the Blade as $blogPost
        'allPosts' => $allPosts,
        'linkedIds' => $linkedArticleIds,
    ]);
}




public function update(Request $request, BlogPost $blog)
{
    $data = $request->validate([
        'title' => 'required|string|max:255',
        'slug' => 'required|string|unique:blog_posts,slug,' . $blog->id,
        'content' => 'required|string',
        'meta_description' => 'nullable|string|max:255',
        'linked_articles' => 'nullable|array',
        'cover_image' => 'nullable|image|max:2048',
    ]);

    if ($request->hasFile('cover_image')) {
        if ($blog->cover_image) {
            \Storage::disk('public')->delete($blog->cover_image);
        }
        $data['cover_image'] = $request->file('cover_image')->store('blog_covers', 'public');
    }

    $blog->update($data);

    $blog->linkedArticles()->sync($data['linked_articles'] ?? []);

    return redirect()->route('admin.blog.index')->with('success', 'Blog post updated.');
}


public function destroy(BlogPost $blog)
{
    if ($blog->cover_image) {
        \Storage::disk('public')->delete($blog->cover_image);
    }

    $blog->linkedArticles()->detach();
    $blog->delete();

    return redirect()->route('admin.blog.index')->with('success', 'Blog post deleted.');
}

}
