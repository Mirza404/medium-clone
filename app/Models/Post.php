<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Post extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        // 'image',
        'title',
        'slug',
        'content',
        'category_id',
        'user_id',
        'published_at',
    ];

    public function registerMediaConversions(?Media $media = null): void
    {
        $this
            ->addMediaConversion('preview')
            ->width(400);

        $this
            ->addMediaConversion('large')
            ->width(1200);
    }

    public function user(){
        return $this->belongsTo(User::class); 
    }

    public function category(){
        return $this->belongsTo(Category::class);
    }

    public function readTime($wordsPerMinute = 100){
        $wordCount = str_word_count(strip_tags($this->content));
        $minutes = ceil($wordCount / $wordsPerMinute); // Assuming average reading speed of 200 words per minute
        return max(1, $minutes);
    }

    public function imageUrl($preview = false){
        if($this->image){
            return Storage::url($this->image);
        } else{
            return null;
        }
    }

    public function claps(){
        return $this->hasMany(Clap::class);
    }
}
