<?php

namespace App\Data;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $casts = [
        'premium' => 'active',
        'premium' => 'moderated'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'slug', 'seo_title', 'excerpt', 'body', 'meta_description', 'meta_keywords', 'active', 'image', 'user_id', 'moderated'
    ];
}
