<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserLike extends Model
{
    protected $table = 'user_likes';

    protected $fillable = [
        'user_id', 'type', 'spotify_id',
        'name', 'artist_name', 'image_url', 'external_url', 'extra',
    ];

    protected $casts = [
        'extra' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
