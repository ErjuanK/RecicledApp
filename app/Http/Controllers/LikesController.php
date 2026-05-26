<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserLike;
use Illuminate\Support\Facades\Auth;
use App\Services\ItunesService;
use Illuminate\Support\Facades\Log;

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

    /**
     * Importar playlist desde texto pegado por el usuario.
     * Máximo 100 canciones por importación.
     * Body JSON: { text: "..." }
     */
    public function import(Request $request, ItunesService $itunes)
    {
        $request->validate([
            'text' => 'required|string'
        ]);

        $userId = Auth::id();
        $text = $request->input('text');
        $lines = preg_split('/\r\n|\r|\n/', $text);
        
        // Filtrar líneas vacías y contar líneas válidas
        $validLines = array_filter(array_map('trim', $lines), fn($l) => $l !== '');
        
        // Validar límite de 100 canciones
        if (count($validLines) > 100) {
            return response()->json([
                'error' => 'Máximo 100 canciones por importación',
                'max' => 100,
                'submitted' => count($validLines)
            ], 422);
        }

        $counts = ['songs' => 0, 'albums' => 0, 'artists' => 0];
        $errors = [];

        foreach ($lines as $raw) {
            $line = trim($raw);
            if ($line === '') continue;

            // Normalize common separators and remove extraneous info
            $clean = preg_replace('/\s*[–—•\|\/]\s*/u', ' - ', $line);
            // Remove content inside parentheses or brackets (e.g. (Live), [Remix])
            $clean = preg_replace('/\s*\([^\)]*\)|\s*\[[^\]]*\]/', '', $clean);
            $clean = trim($clean);

            // Heuristics: try multiple queries in order
            $searchQueries = [];

            // If contains ' - ', try split Title - Artist
            if (strpos($clean, ' - ') !== false) {
                [$maybeTitle, $maybeArtist] = array_map('trim', explode(' - ', $clean, 2));
                if ($maybeTitle && $maybeArtist) {
                    $searchQueries[] = $maybeTitle . ' ' . $maybeArtist;
                    $searchQueries[] = $maybeTitle . ' ' . $maybeArtist . ' ' . ($maybeArtist ?: '');
                    $searchQueries[] = $maybeTitle;
                }
            }

            // Try "by" patterns e.g. "Title by Artist"
            if (preg_match('/^(.*)\s+by\s+(.*)$/i', $clean, $m)) {
                $searchQueries[] = trim($m[1]) . ' ' . trim($m[2]);
            }

            // Fallbacks
            $searchQueries[] = $clean;
            // also try removing quotes
            $searchQueries[] = trim($clean, '"\'');

            $found = false;
            $result = null;

            foreach ($searchQueries as $q) {
                if ($found) break;
                if (strlen($q) < 2) continue;
                try {
                    $res = $itunes->searchTracks($q, 1);
                } catch (\Exception $e) {
                    Log::error('Import search error: ' . $e->getMessage());
                    $res = [];
                }

                if (!empty($res) && isset($res[0]['id'])) {
                    $found = true;
                    $result = $res[0];
                    break;
                }
            }

            if (!$found || !$result) {
                $errors[] = $line;
                continue;
            }

            // Save song
            $t = $result;
            $songId = $t['id'] ?? ($t['external_urls']['spotify'] ?? md5($t['name'] . ($t['artists'][0]['name'] ?? '')));
            $songName = $t['name'] ?? $line;
            $artistName = $t['artists'][0]['name'] ?? null;
            $image = $t['album']['images'][0]['url'] ?? ($t['images'][0]['url'] ?? null);
            $external = $t['external_urls']['spotify'] ?? null;

            $song = UserLike::updateOrCreate(
                ['user_id' => $userId, 'type' => 'song', 'spotify_id' => $songId],
                ['name' => $songName, 'artist_name' => $artistName, 'image_url' => $image, 'external_url' => $external]
            );
            $counts['songs']++;

            // Save album if present
            if (!empty($t['album']['id'])) {
                $albumId = $t['album']['id'];
                $albumName = $t['album']['name'] ?? null;
                $albumImage = $t['album']['images'][0]['url'] ?? $image;
                $albumExternal = $t['album']['external_urls']['spotify'] ?? null;

                UserLike::updateOrCreate(
                    ['user_id' => $userId, 'type' => 'album', 'spotify_id' => $albumId],
                    ['name' => $albumName, 'artist_name' => $artistName, 'image_url' => $albumImage, 'external_url' => $albumExternal]
                );
                $counts['albums']++;
            }

            // Save artist if present
            if (!empty($t['artists'][0]['id'])) {
                $aId = $t['artists'][0]['id'];
                $aName = $t['artists'][0]['name'] ?? $artistName;

                UserLike::updateOrCreate(
                    ['user_id' => $userId, 'type' => 'artist', 'spotify_id' => $aId],
                    ['name' => $aName, 'artist_name' => null, 'image_url' => null, 'external_url' => null]
                );
                $counts['artists']++;
            }
        }

        return response()->json([
            'success' => true,
            'imported' => $counts,
            'total' => count($validLines),
            'message' => "Importadas {$counts['songs']} canciones, {$counts['albums']} álbumes y {$counts['artists']} artistas",
            'errors' => $errors
        ]);
    }
}
