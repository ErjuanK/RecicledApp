<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use DOMDocument;
use DOMXPath;

class GeniusService
{
    private $tokenAcceso;

    public function __construct()
    {
        $this->tokenAcceso = config('services.genius.access_token');
    }

    private function realizarPeticion($url)
    {
        $ch = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $this->tokenAcceso
            ],
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TIMEOUT => 30
        ]);

        $respuesta = curl_exec($ch);
        $codigoHttp = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return ($codigoHttp === 200) ? json_decode($respuesta, true) : null;
    }

    public function buscarCancion($termino)
    {
        $url = "https://api.genius.com/search?q=" . urlencode($termino);
        $datos = $this->realizarPeticion($url);

        if ($datos && isset($datos['response']['hits']) && count($datos['response']['hits']) > 0) {
            return $datos['response']['hits'][0]['result'];
        }

        return null;
    }

    public function obtenerLetra($url)
    {
        $headers = [
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8',
            'Accept-Language: es-ES,es;q=0.9,en;q=0.8',
            'Accept-Encoding: gzip, deflate, br',
            'Cache-Control: no-cache',
            'Pragma: no-cache',
            'Referer: https://www.google.com/',
            'sec-ch-ua: "Chromium";v="122", "Not(A:Brand";v="24", "Google Chrome";v="122"',
            'sec-ch-ua-mobile: ?0',
            'sec-ch-ua-platform: "Windows"',
            'Sec-Fetch-Dest: document',
            'Sec-Fetch-Mode: navigate',
            'Sec-Fetch-Site: cross-site',
            'Upgrade-Insecure-Requests: 1',
            'Connection: keep-alive',
        ];

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_CONNECTTIMEOUT => 10,
        ]);

        $html = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if (!$html || in_array($httpCode, [403, 503])) {
            Log::warning("Genius: Bloqueado por Cloudflare (HTTP {$httpCode})");
            return null;
        }

        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($html);
        libxml_clear_errors();

        $xpath = new DOMXPath($dom);
        $nodos = $xpath->query('//div[@data-lyrics-container="true"]');

        if ($nodos->length === 0) return null;

        $htmlLetra = '';
        foreach ($nodos as $nodo) {
            $innerHtml = $dom->saveHTML($nodo);
            $textoLimpio = strip_tags($innerHtml, '<br>');
            $htmlLetra .= $textoLimpio . '<br>';
        }

        // Eliminar metadatos de Genius
        $htmlLetra = preg_replace('/^\s*\d+\s*Contributors.*?[\r\n]+.*?(?=\[)/s', '', $htmlLetra);
        if (stripos($htmlLetra, 'Contributors') !== false) {
            $htmlLetra = preg_replace('/^.*?Contributors.*?((?=\[)|$)/s', '', $htmlLetra);
        }
        $htmlLetra = preg_replace('/^\s*\[(Letra|Lyrics)[^\]]*\]\s*/i', '', $htmlLetra);
        $htmlLetra = preg_replace('/(\[.*?\])/', '<span class="etiqueta-cancion">$1</span>', $htmlLetra);
        $htmlLetra = preg_replace('/^(\s*<br\s*\/?>\s*)+/i', '', $htmlLetra);

        return $htmlLetra;
    }

    /**
     * Fallback: busca la letra en LRCLIB.net (API pública y gratuita, sin bloqueos de IP)
     * Se usa cuando Genius está bloqueado por Cloudflare en producción.
     * @param string $artista Nombre del artista
     * @param string $titulo Título de la canción
     * @return string|null Letra en texto plano o null si no se encuentra
     */
    public function obtenerLetraFallback($artista, $titulo)
    {
        try {
            $letraLrclib = $this->buscarEnLrclib($artista, $titulo);
            if ($letraLrclib) {
                Log::info("Letra encontrada en LRCLIB: {$titulo}");
                return $letraLrclib;
            }

            $letraOvh = $this->buscarEnLyricsOvh($artista, $titulo);
            if ($letraOvh) {
                Log::info("Letra encontrada en Lyrics.ovh: {$titulo}");
                return $letraOvh;
            }
        } catch (\Exception $e) {
            Log::error("Error en fallback de letras: " . $e->getMessage());
        }

        return null;
    }

    private function buscarEnLrclib($artista, $titulo)
    {
        $url = 'https://lrclib.net/api/get?' . http_build_query([
            'artist_name' => $artista,
            'track_name' => $titulo,
        ]);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ['Accept: application/json'],
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TIMEOUT => 10,
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200 || !$response) return null;

        $data = json_decode($response, true);
        $texto = $data['plainLyrics'] ?? $data['syncedLyrics'] ?? null;

        if (!$texto) return null;

        $html = nl2br(htmlspecialchars($texto));
        $html = preg_replace('/(\[.*?\])/', '<span class="etiqueta-cancion">$1</span>', $html);

        return $html;
    }

    private function buscarEnLyricsOvh($artista, $titulo)
    {
        $url = 'https://api.lyrics.ovh/v1/' . urlencode($artista) . '/' . urlencode($titulo);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TIMEOUT => 10,
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200 || !$response) return null;

        $data = json_decode($response, true);
        if (empty($data['lyrics'])) return null;

        $html = nl2br(htmlspecialchars($data['lyrics']));
        $html = preg_replace('/(\[.*?\])/', '<span class="etiqueta-cancion">$1</span>', $html);

        return $html;
    }

    public function obtenerIdArtista($nombre)
    {
        return Cache::remember('genius.artist_id.' . md5($nombre), 2592000, function () use ($nombre) {
            $url = "https://api.genius.com/search?q=" . urlencode($nombre);
            $datos = $this->realizarPeticion($url);

            if ($datos && isset($datos['response']['hits'][0]['result']['primary_artist']['id'])) {
                return $datos['response']['hits'][0]['result']['primary_artist']['id'];
            }

            return null;
        });
    }

    public function obtenerBiografia($id)
    {
        return Cache::remember("genius.artist.{$id}.bio", 2592000, function () use ($id) {
            $url = "https://api.genius.com/artists/{$id}";
            $datos = $this->realizarPeticion($url);

            if (!$datos || !isset($datos['response']['artist'])) {
                return null;
            }

            $artist = $datos['response']['artist'];
            $description = $artist['description'] ?? null;

            if (!$description) return null;

            // Mayoría de APIs devuelven 'plain'
            if (isset($description['plain']) && !empty($description['plain'])) {
                return $description['plain'];
            }

            // Fallback: HTML limpio
            if (isset($description['html']) && !empty($description['html'])) {
                $bioClean = strip_tags($description['html']);
                $bioClean = preg_replace('/\s+/', ' ', $bioClean);
                return trim($bioClean);
            }

            if (is_string($description)) {
                return strip_tags($description);
            }

            // Parsear estructura DOM compleja
            if (isset($description['dom'])) {
                $bioText = $this->parsearDomGenius($description['dom']);
                if ($bioText) {
                    $bioEspanol = $this->traducirTexto($bioText);
                    return $bioEspanol ?: $bioText;
                }
            }

            return null;
        });
    }

    private function traducirTexto($texto)
    {
        try {
            $url = "https://translate.googleapis.com/translate_a/single?client=gtx&sl=auto&tl=es&dt=t&q=" . urlencode($texto);

            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_USERAGENT => 'Mozilla/5.0',
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_TIMEOUT => 10
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200 && $response) {
                $data = json_decode($response, true);

                if (isset($data[0]) && is_array($data[0])) {
                    $traduccion = '';
                    foreach ($data[0] as $segment) {
                        if (isset($segment[0])) {
                            $traduccion .= $segment[0];
                        }
                    }

                    return !empty($traduccion) ? $traduccion : null;
                }
            }
        } catch (\Exception $e) {
            Log::error("Error al traducir: " . $e->getMessage());
        }

        return null;
    }

    private function parsearDomGenius($dom, &$parrafosContados = 0)
    {
        if (!is_array($dom) || !isset($dom['children'])) {
            return null;
        }

        $texto = '';
        $maxParrafos = 2;

        foreach ($dom['children'] as $child) {
            if ($parrafosContados >= $maxParrafos) break;

            if (is_string($child)) {
                $texto .= $child;
            } elseif (is_array($child)) {
                if (isset($child['tag'])) {
                    if ($child['tag'] === 'a') {
                        if (isset($child['children'])) {
                            $texto .= $this->parsearDomGenius($child, $parrafosContados);
                        }
                    } elseif ($child['tag'] === 'br') {
                        $texto .= "\n";
                    } elseif ($child['tag'] === 'p') {
                        if (isset($child['children'])) {
                            $parrafosContados++;
                            $texto .= $this->parsearDomGenius($child, $parrafosContados) . "\n\n";

                            if ($parrafosContados >= $maxParrafos) break;
                        }
                    } else {
                        if (isset($child['children'])) {
                            $texto .= $this->parsearDomGenius($child, $parrafosContados);
                        }
                    }
                } elseif (isset($child['children'])) {
                    $texto .= $this->parsearDomGenius($child, $parrafosContados);
                }
            }
        }

        $texto = preg_replace('/\n{3,}/', "\n\n", $texto);
        return trim($texto);
    }
}
