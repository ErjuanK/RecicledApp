<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserSongAction;
use App\Models\UserLike;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;


class ForYouController extends Controller
{
    // Carga la vista principal
    public function index() {
        return view('discovery.foryou');
    }

    /**
     * Devuelve la siguiente canción usando la API pública de iTunes.
     * iTunes siempre incluye previewUrl de 30 segundos, sin registro ni token.
     */
    public function getNextTrack()
    {
        $user = Auth::user();

        // Canciones ya vistas por el usuario (base de datos)
        $seenTrackIds = UserSongAction::where('user_id', $user->id)
            ->pluck('spotify_track_id')
            ->toArray();

        // Canciones que tenemos en la cola del frontend (parámetro de URL)
        $excludedFromFront = request()->query('exclude', '');
        if (!empty($excludedFromFront)) {
            $seenTrackIds = array_merge($seenTrackIds, array_filter(explode(',', $excludedFromFront)));
        }

        // Seeds de búsqueda: usar gustos de sesión o géneros populares por defecto
        $musicSeeds = session('music_seeds');
        $genres = [];

        if (!empty($musicSeeds)) {
            $genres = is_array($musicSeeds) ? $musicSeeds : [$musicSeeds];
        }

        // Géneros de fallback si no hay gustos definidos
        $defaultGenres = [
            'pop latino', 'reggaeton', 'pop español', 'trap español',
            'flamenco pop', 'indie pop', 'hits 2024', 'bad bunny',
            'rosalía', 'c. tangana', 'maluma', 'j balvin',
        ];

        // Mezclar géneros reales con los de fallback para mayor variedad
        $allSeeds = array_merge(
            array_slice($genres, 0, 5),
            array_slice($defaultGenres, 0, max(0, 12 - count($genres)))
        );
        shuffle($allSeeds);

        // Buscar en iTunes hasta encontrar una canción no vista con preview
        $nextTrack = null;

        foreach ($allSeeds as $seed) {
            $candidates = $this->searchiTunes($seed, 25);

            // Mezclar resultados para no dar siempre la primera canción
            shuffle($candidates);

            foreach ($candidates as $track) {
                $trackId = $track['id'];

                // Saltamos si ya la vio
                if (in_array($trackId, $seenTrackIds)) {
                    continue;
                }

                // ¡Tenemos candidata válida con audio garantizado!
                $nextTrack = $track;
                break;
            }

            if ($nextTrack) break;
        }

        if (!$nextTrack) {
            Log::warning("ForYouController: No iTunes tracks found for user {$user->id}");
            return response()->json(['error' => 'no_tracks']);
        }

        return response()->json($nextTrack);
    }

    /**
     * Busca canciones en la API pública de iTunes (sin autenticación, audio garantizado).
     * Normaliza la respuesta al formato que espera el frontend.
     */
    private function searchiTunes(string $term, int $limit = 25): array
    {
        $cacheKey = 'itunes_search_' . md5($term . $limit);

        return Cache::remember($cacheKey, 3600, function () use ($term, $limit) {
            $query = urlencode($term);
            $url = "https://itunes.apple.com/search?term={$query}&entity=song&country=es&limit={$limit}";

            $ctx = stream_context_create([
                'http' => [
                    'timeout' => 8,
                    'user_agent' => 'Mozilla/5.0',
                ]
            ]);

            $json = @file_get_contents($url, false, $ctx);
            if (!$json) return [];

            $data = json_decode($json, true);
            if (empty($data['results'])) return [];

            $tracks = [];
            foreach ($data['results'] as $item) {
                // Solo canciones con preview de audio
                if (empty($item['previewUrl'])) continue;

                // Normalizar al formato que usa el frontend (compatible con Spotify objects)
                $tracks[] = [
                    'id'          => 'itunes_' . $item['trackId'],
                    'name'        => $item['trackName'] ?? 'Unknown',
                    'preview_url' => $item['previewUrl'],
                    'external_urls' => [
                        'spotify' => $item['trackViewUrl'] ?? '#'
                    ],
                    'artists' => [
                        ['name' => $item['artistName'] ?? 'Unknown', 'id' => 'itunes_artist_' . ($item['artistId'] ?? 0)]
                    ],
                    'album' => [
                        'id'     => 'itunes_album_' . ($item['collectionId'] ?? 0),
                        'name'   => $item['collectionName'] ?? '',
                        'images' => [
                            // Subir resolución de la miniatura de iTunes (100x100 → 600x600)
                            ['url' => str_replace('100x100bb', '600x600bb', $item['artworkUrl100'] ?? '')],
                            ['url' => str_replace('100x100bb', '300x300bb', $item['artworkUrl100'] ?? '')],
                            ['url' => $item['artworkUrl100'] ?? ''],
                        ],
                    ],
                    'duration_ms'  => ($item['trackTimeMillis'] ?? 0),
                ];
            }
            return $tracks;
        });
    }

    // API para guardar el Swipe
    public function handleAction(Request $request)
    {
        $request->validate([
            'track_id'    => 'required|string',
            'album_id'    => 'nullable|string',
            'action'      => 'required|in:like,dislike',
            // Metadatos opcionales para guardar en user_likes
            'track_name'  => 'nullable|string',
            'artist_name' => 'nullable|string',
            'image_url'   => 'nullable|string',
            'preview_url' => 'nullable|string',
            'external_url'=> 'nullable|string',
        ]);

        $userId = Auth::id();

        // Siempre guardamos en UserSongAction para el motor de recomendaciones
        UserSongAction::updateOrCreate(
            ['user_id' => $userId, 'spotify_track_id' => $request->track_id],
            ['album_id' => $request->album_id, 'action' => $request->action]
        );

        // Si es un LIKE, también lo guardamos en user_likes para "Mis Me Gustas"
        if ($request->action === 'like' && $request->track_name) {
            UserLike::updateOrCreate(
                ['user_id' => $userId, 'type' => 'song', 'spotify_id' => $request->track_id],
                [
                    'name'         => $request->track_name,
                    'artist_name'  => $request->artist_name,
                    'image_url'    => $request->image_url,
                    'external_url' => $request->external_url,
                    'extra'        => ['preview_url' => $request->preview_url],
                ]
            );
        }

        // Si era DISLIKE y existía un like previo, lo eliminamos
        if ($request->action === 'dislike') {
            UserLike::where('user_id', $userId)
                ->where('type', 'song')
                ->where('spotify_id', $request->track_id)
                ->delete();
        }

        return response()->json(['status' => 'success']);
    }
}
