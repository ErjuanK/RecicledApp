<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class GeniusService {
    
    private $tokenAcceso;

    public function __construct() {
        // Token obtenido desde variables de entorno seguras
        $this->tokenAcceso = config('services.genius.access_token');
    }

    /**
     * Realiza una petición GET a la API de Genius
     * @param string $url La URL completa del endpoint
     * @return array|null La respuesta decodificada o null si falla
     */
    private function realizarPeticion($url) {
        $ch = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $this->tokenAcceso
            ],
            CURLOPT_SSL_VERIFYPEER => false, // Desactivar verificación SSL para entorno local si es necesario
            CURLOPT_TIMEOUT => 30
        ]);

        $respuesta = curl_exec($ch);
        $codigoHttp = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if (curl_errno($ch)) {
            echo 'Error cURL: ' . curl_error($ch) . PHP_EOL;
        }

        curl_close($ch);

        if ($codigoHttp === 200) {
            return json_decode($respuesta, true);
        }

        return null;
    }

    /**
     * Verifica la conexión con la API realizando una búsqueda de prueba
     */
    public function verificarConexion() {
        echo "Iniciando verificación de conexión con Genius API..." . PHP_EOL;

        $terminoBusqueda = "Queen Bohemian Rhapsody";
        $url = "https://api.genius.com/search?q=" . urlencode($terminoBusqueda);

        $datos = $this->realizarPeticion($url);

        if ($datos && isset($datos['response']['hits']) && count($datos['response']['hits']) > 0) {
            $primerResultado = $datos['response']['hits'][0]['result'];
            echo "¡Éxito! Conexión establecida correctamente." . PHP_EOL;
            echo "Primera canción encontrada: " . $primerResultado['full_title'] . PHP_EOL;
            echo "Artista: " . $primerResultado['artist_names'] . PHP_EOL;
        } else {
            echo "Error: No se pudo conectar a la API o no se encontraron resultados." . PHP_EOL;
            if (!$datos) {
                echo "La respuesta de la API fue nula." . PHP_EOL;
            }
        }
    }

    /**
     * Busca una canción en la API de Genius y devuelve el primer resultado
     * @param string $termino Término de búsqueda (ej: "Queen Bohemian Rhapsody")
     * @return array|null Datos de la canción o null si no se encuentra
     */
    public function buscarCancion($termino) {
        $url = "https://api.genius.com/search?q=" . urlencode($termino);
        $datos = $this->realizarPeticion($url);

        if ($datos && isset($datos['response']['hits']) && count($datos['response']['hits']) > 0) {
            return $datos['response']['hits'][0]['result'];
        }

        return null;
    }

    /**
     * Obtiene la letra real de la canción haciendo scraping a la URL de Genius
     * @param string $url URL de la canción en Genius
     * @return string|null HTML de la letra o null si falla
     */
    public function obtenerLetra($url) {
        // Headers robustos que simulan Chrome real para evitar bloqueos de Cloudflare en producción
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
            CURLOPT_ENCODING       => '', // Permite descomprimir gzip/br automáticamente
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_CONNECTTIMEOUT => 10,
        ]);

        $html = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Si Cloudflare bloquea (403) o no hay respuesta, devolver null para activar el fallback
        if (!$html || $httpCode === 403 || $httpCode === 503) {
            \Log::warning("GeniusService: Bloqueado por Cloudflare en producción (HTTP {$httpCode}). Activar fallback.");
            return null;
        }

        $dom = new \DOMDocument();
        libxml_use_internal_errors(true); // Suprimir advertencias de HTML mal formado
        $dom->loadHTML($html);
        libxml_clear_errors();

        $xpath = new \DOMXPath($dom);
        // Buscar contenedores de letras (modern Genius frontend)
        $nodos = $xpath->query('//div[@data-lyrics-container="true"]');

        if ($nodos->length === 0) return null;

        $htmlLetra = '';
        foreach ($nodos as $nodo) {
            $innerHtml = $dom->saveHTML($nodo);
            $textoLimpio = strip_tags($innerHtml, '<br>');
            $htmlLetra .= $textoLimpio . '<br>';
        }

        // Limpieza de metadatos de Genius
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
    public function obtenerLetraFallback($artista, $titulo) {
        try {
            // Intentar primero con LRCLIB
            $letraLrclib = $this->buscarEnLrclib($artista, $titulo);
            if ($letraLrclib) {
                \Log::info("GeniusService: Letra encontrada en LRCLIB para '{$titulo}' de '{$artista}'");
                return $letraLrclib;
            }

            // Segundo fallback: Lyrics.ovh
            $letraOvh = $this->buscarEnLyricsOvh($artista, $titulo);
            if ($letraOvh) {
                \Log::info("GeniusService: Letra encontrada en Lyrics.ovh para '{$titulo}' de '{$artista}'");
                return $letraOvh;
            }
        } catch (\Exception $e) {
            \Log::error("GeniusService: Error en fallback de letras: " . $e->getMessage());
        }

        return null;
    }

    /**
     * Busca letra en LRCLIB.net
     */
    private function buscarEnLrclib($artista, $titulo) {
        $url = 'https://lrclib.net/api/get?' . http_build_query([
            'artist_name' => $artista,
            'track_name'  => $titulo,
        ]);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => ['Accept: application/json'],
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TIMEOUT        => 10,
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200 || !$response) return null;

        $data = json_decode($response, true);

        // Preferir letra sin tiempo (plainLyrics) sobre la sincronizada (syncedLyrics)
        $texto = $data['plainLyrics'] ?? $data['syncedLyrics'] ?? null;
        if (!$texto) return null;

        // Convertir texto plano a HTML con saltos de línea
        $html = nl2br(htmlspecialchars($texto));
        // Envolver etiquetas [Intro], [Coro], [Verso] en span estilizable
        $html = preg_replace('/(\[.*?\])/', '<span class="etiqueta-cancion">$1</span>', $html);

        return $html;
    }

    /**
     * Busca letra en Lyrics.ovh como segundo fallback
     */
    private function buscarEnLyricsOvh($artista, $titulo) {
        $url = 'https://api.lyrics.ovh/v1/' . urlencode($artista) . '/' . urlencode($titulo);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TIMEOUT        => 10,
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

    /**
     * Obtiene el ID del artista en Genius buscando por nombre
     * @param string $nombre Nombre del artista
     * @return int|null ID del artista o null
     */
    public function obtenerIdArtista($nombre) {
        // Cache por 30 días (Genius IDs no cambian)
        return Cache::remember('genius.artist_id.' . md5($nombre), 2592000, function () use ($nombre) {
            $url = "https://api.genius.com/search?q=" . urlencode($nombre);
            $datos = $this->realizarPeticion($url);
            
            // Debug logging
            error_log("Genius API - Buscando artista: {$nombre}");
            error_log("Genius API - URL: {$url}");
            
            if ($datos) {
                error_log("Genius API - Respuesta recibida: " . json_encode($datos));
            } else {
                error_log("Genius API - No se recibió respuesta");
            }
            
            // Asumimos que el primer resultado coincide con el artista buscado
            if ($datos && isset($datos['response']['hits'][0]['result']['primary_artist']['id'])) {
                $artistId = $datos['response']['hits'][0]['result']['primary_artist']['id'];
                error_log("Genius API - ID del artista encontrado: {$artistId}");
                return $artistId;
            }
            
            error_log("Genius API - No se encontró ID del artista");
            return null;
        });
    }

    /**
     * Obtiene la biografía del artista desde Genius
     * @param int $id ID del artista en Genius
     * @return string|null Biografía en texto plano o null
     */
    public function obtenerBiografia($id) {
        // Cache por 30 días (biografías cambian poco)
        return Cache::remember("genius.artist.{$id}.bio", 2592000, function () use ($id) {
            $url = "https://api.genius.com/artists/{$id}";
            $datos = $this->realizarPeticion($url);
            
            error_log("Genius API - Obteniendo biografía para ID: {$id}");
            
            if (!$datos || !isset($datos['response']['artist'])) {
                error_log("Genius API - No se encontró información del artista");
                return null;
            }
            
            $artist = $datos['response']['artist'];
            $description = $artist['description'] ?? null;
            
            if (!$description) {
                error_log("Genius API - Campo 'description' no existe");
                return null;
            }
            
            // Estrategia 1: Intentar 'plain' (texto plano)
            if (isset($description['plain']) && !empty($description['plain'])) {
                error_log("Genius API - Biografía encontrada en 'plain'");
                return $description['plain'];
            }
            
            // Estrategia 2: Intentar 'html' y limpiar tags
            if (isset($description['html']) && !empty($description['html'])) {
                error_log("Genius API - Biografía encontrada en 'html', limpiando tags");
                $bioHtml = $description['html'];
                // Strip all HTML tags including links
                $bioClean = strip_tags($bioHtml);
                // Clean up extra whitespace
                $bioClean = preg_replace('/\s+/', ' ', $bioClean);
                $bioClean = trim($bioClean);
                return $bioClean;
            }
            
            // Estrategia 3: Si 'description' es string directamente
            if (is_string($description)) {
                error_log("Genius API - 'description' es string directo");
                return strip_tags($description);
            }
            
            // Estrategia 4: Parsear 'dom' (estructura compleja de Genius)
            if (isset($description['dom'])) {
                error_log("Genius API - Parseando biografía en formato 'dom'");
                $bioText = $this->parsearDomGenius($description['dom']);
                if ($bioText) {
                    // Traducir al español
                    $bioEspanol = $this->traducirTexto($bioText);
                    return $bioEspanol ?: $bioText; // Si falla traducción, devolver original
                }
            }
            
            error_log("Genius API - No se pudo extraer biografía de ningún formato");
            error_log("Genius API - Estructura de description: " . json_encode(array_keys($description)));
            return null;
        });
    }
    
    /**
     * Traduce texto al español usando Google Translate
     * @param string $texto Texto a traducir
     * @return string|null Texto traducido o null si falla
     */
    private function traducirTexto($texto) {
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
                    
                    if (!empty($traduccion)) {
                        error_log("Genius API - Biografía traducida al español");
                        return $traduccion;
                    }
                }
            }
            
            error_log("Genius API - Error al traducir biografía");
            return null;
            
        } catch (Exception $e) {
            error_log("Genius API - Excepción al traducir: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Parsea el formato DOM de Genius para extraer texto plano
     * @param array $dom Estructura DOM de Genius
     * @param int &$parrafosContados Contador de párrafos (por referencia)
     * @return string|null Texto extraído o null
     */
    private function parsearDomGenius($dom, &$parrafosContados = 0) {
        if (!is_array($dom) || !isset($dom['children'])) {
            return null;
        }
        
        $texto = '';
        $maxParrafos = 2; // Limitar a 2 párrafos
        
        foreach ($dom['children'] as $child) {
            // Si ya tenemos 2 párrafos, detener
            if ($parrafosContados >= $maxParrafos) {
                break;
            }
            
            if (is_string($child)) {
                // Texto directo
                $texto .= $child;
            } elseif (is_array($child)) {
                // Nodo con estructura
                if (isset($child['tag'])) {
                    // Ignorar enlaces (tag 'a')
                    if ($child['tag'] === 'a') {
                        // Extraer solo el texto del enlace, no la URL
                        if (isset($child['children'])) {
                            $texto .= $this->parsearDomGenius($child, $parrafosContados);
                        }
                    } elseif ($child['tag'] === 'br') {
                        $texto .= "\n";
                    } elseif ($child['tag'] === 'p') {
                        // Párrafo - incrementar contador
                        if (isset($child['children'])) {
                            $parrafosContados++;
                            $texto .= $this->parsearDomGenius($child, $parrafosContados) . "\n\n";
                            
                            // Si ya tenemos 2 párrafos, detener
                            if ($parrafosContados >= $maxParrafos) {
                                break;
                            }
                        }
                    } else {
                        // Otros tags, extraer contenido
                        if (isset($child['children'])) {
                            $texto .= $this->parsearDomGenius($child, $parrafosContados);
                        }
                    }
                } elseif (isset($child['children'])) {
                    // Nodo sin tag específico
                    $texto .= $this->parsearDomGenius($child, $parrafosContados);
                }
            }
        }
        
        // Limpiar espacios extra
        $texto = preg_replace('/\n{3,}/', "\n\n", $texto);
        $texto = trim($texto);
        
        return $texto;
    }
}
