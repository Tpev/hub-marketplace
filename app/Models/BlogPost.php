<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogPost extends Model
{
protected $fillable = ['title', 'slug', 'content', 'cover_image', 'linked_articles', 'meta_description'];

protected $casts = [
    'linked_articles' => 'array',
];
// BlogPost.php
public function linkedArticles()
{
    return $this->belongsToMany(BlogPost::class, 'blog_post_links', 'blog_post_id', 'linked_post_id');
}

}
