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
        // Simular navegador real para evitar bloqueos simples
        $user_agent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36';

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERAGENT => $user_agent,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_FOLLOWLOCATION => true
        ]);

        $html = curl_exec($ch);
        curl_close($ch);

        if (!$html) return null;

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
            // Obtener el HTML interno
            $innerHtml = $dom->saveHTML($nodo);
            
            // Limpiar etiquetas no deseadas, dejando solo saltos de línea para mantener estructura
            $textoLimpio = strip_tags($innerHtml, '<br>');
            
            $htmlLetra .= $textoLimpio . '<br>';
        }

        // Limpieza específica de metadatos de Genius (encabezados de colaboradores, etc.)
        // Patrón: Elimina desde el inicio hasta encontrar "Lyrics" o "[Letra" si va precedido de Contributors, etc.
        $htmlLetra = preg_replace('/^\s*\d+\s*Contributors.*?[\r\n]+.*?(?=\[)/s', '', $htmlLetra);
        
        // Limpiar también el "Read More" si queda suelto o cualquier texto antes del primer corchete si parece basura
        // A veces Genius pone "X Contributors ... Lyrics [Intro]"
        // Vamos a intentar quitar todo antes del primer bloque entre corchetes si detectamos "Contributors"
        // Vamos a intentar quitar todo antes del primer bloque entre corchetes si detectamos "Contributors"
        if (stripos($htmlLetra, 'Contributors') !== false) {
             $htmlLetra = preg_replace('/^.*?Contributors.*?((?=\[)|$)/s', '', $htmlLetra);
        }

        // Eliminar específicamente etiqueta inicial tipo [Letra de "Moscow Mule"] o similar
        $htmlLetra = preg_replace('/^\s*\[(Letra|Lyrics)[^\]]*\]\s*/i', '', $htmlLetra);

        // Envolver etiquetas como [Intro], [Coro], [Verso] en un span para estilizarlas
        $htmlLetra = preg_replace('/(\[.*?\])/', '<span class="etiqueta-cancion">$1</span>', $htmlLetra);

        // Limpiar saltos de línea iniciales acumulados
        $htmlLetra = preg_replace('/^(\s*<br\s*\/?>\s*)+/i', '', $htmlLetra);

        return $htmlLetra;
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
