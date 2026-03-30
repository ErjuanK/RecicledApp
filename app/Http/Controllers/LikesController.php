<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserLike;
use Illuminate\Support\Facades\Auth;

class LikesController extends Controller
{
    /** Página principal de "Mis Me Gustas" */
    public function index()
    {
        $userId = Auth::id();

        $songs   = UserLike::where('user_id', $userId)->where('type', 'song')  ->latest()->get();
        $albums  = UserLike::where('user_id', $userId)->where('type', 'album') ->latest()->get();
        $artists = UserLike::where('user_id', $userId)->where('type', 'artist')->latest()->get();

        return view('likes.index', compact('songs', 'albums', 'artists'));
    }

    /**
     * Toggle like: si ya existe lo elimina, si no lo crea.
     * Body JSON: { type, spotify_id, name, artist_name?, image_url?, external_url?, extra? }
     */
    public function toggle(Request $request)
    {
        $request->validate([
            'type'       => 'required|in:song,album,artist',
            'spotify_id' => 'required|string',
            'name'       => 'required|string',
        ]);

        $userId = Auth::id();

        $existing = UserLike::where('user_id', $userId)
            ->where('type', $request->type)
            ->where('spotify_id', $request->spotify_id)
            ->first();

        if ($existing) {
            $existing->delete();
            return response()->json(['status' => 'removed', 'liked' => false]);
        }

        UserLike::create([
            'user_id'      => $userId,
            'type'         => $request->type,
            'spotify_id'   => $request->spotify_id,
            'name'         => $request->name,
            'artist_name'  => $request->artist_name,
            'image_url'    => $request->image_url,
            'external_url' => $request->external_url,
            'extra'        => $request->extra,
        ]);

        return response()->json(['status' => 'added', 'liked' => true]);
    }

    /** Comprueba si el usuario ya tiene like en un elemento concreto */
    public function check(Request $request)
    {
        $liked = UserLike::where('user_id', Auth::id())
            ->where('type', $request->type)
            ->where('spotify_id', $request->spotify_id)
            ->exists();

        return response()->json(['liked' => $liked]);
    }

    /** Elimina un like por ID */
    public function destroy($id)
    {
        $like = UserLike::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $like->delete();
        return back()->with('success', 'Like eliminado');
    }
}
