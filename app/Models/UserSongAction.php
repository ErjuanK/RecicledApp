<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSongAction extends Model
{
    protected $fillable = [
        'user_id',
        'spotify_track_id',
        'album_id',
        'action'
    ];
}
