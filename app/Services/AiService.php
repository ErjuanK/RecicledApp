<?php

namespace App\Services;

class AiService
{
    private string $ollamaUrl;
    private string $ollamaModel;
    private int $timeout;

    public function __construct()
    {
        $this->ollamaUrl   = config('services.ollama.url', 'http://localhost:11434');
        $this->ollamaModel = config('services.ollama.model', 'gemma2');
        $this->timeout     = (int) config('services.ollama.timeout', 20);
    }

    /**
     * Analyse the user's musical trinity (genres + albums + tracks) and return
     * a comma-separated list of 5 refined Spotify-compatible seed terms.
     *
     * Falls back gracefully to the original genres if Ollama is unavailable.
     *
     * @param array{genres: string[], albums: string[], tracks: string[]} $data
     * @return string   Comma-separated seeds, e.g. "dark synthwave, dream pop, shoegaze, krautrock, post-punk"
     */
    public function getSmartSeeds(array $data): string
    {
        $genres = implode(', ', $data['genres'] ?? []);
        $albums = implode(', ', $data['albums'] ?? []);
        $tracks = implode(', ', $data['tracks'] ?? []);

        // Fallback value in case Ollama is not available
        $fallback = $genres ?: 'pop';

        if (empty(trim($genres . $albums . $tracks))) {
            return $fallback;
        }

        $prompt = "Actúa como un experto en musicología. El usuario tiene estos gustos musicales actuales:\n"
            . "- Géneros favoritos: $genres\n"
            . "- Álbumes favoritos: $albums\n"
            . "- Canciones que ama: $tracks\n\n"
            . "Basado en esta combinación, identifica el 'vibe' o estilo específico del usuario "
            . "(por ejemplo: Dark Synthwave, Indie Folk melancólico, Afrobeats urbano, etc.). "
            . "Luego, devuélveme exactamente 5 términos de búsqueda técnicos que Spotify entienda "
            . "para encontrar música similar pero que expanda el horizonte del usuario. "
            . "Responde ÚNICAMENTE con los 5 términos separados por comas, sin explicaciones ni texto adicional.";

        try {
            $response = $this->callOllama($prompt);

            if ($response === null) {
                \Log::warning('AiService: Ollama did not respond, using fallback seeds.');
                return $fallback;
            }

            // Clean up the response: remove quotes, newlines, numbering, etc.
            $cleaned = preg_replace('/\d+[\.\)]\s*/', '', $response); // remove "1. " or "1) "
            $cleaned = str_replace(["\n", "\r", '"', "'"], [', ', '', '', ''], $cleaned);
            $cleaned = preg_replace('/,\s*,/', ',', $cleaned);        // remove double commas
            $cleaned = trim($cleaned, " ,\t");

            // Sanity check: if the result is unusable, fall back
            if (strlen($cleaned) < 3) {
                return $fallback;
            }

            \Log::info('AiService: Generated smart seeds → ' . $cleaned);
            return $cleaned;

        } catch (\Throwable $e) {
            \Log::error('AiService: Exception calling Ollama → ' . $e->getMessage());
            return $fallback;
        }
    }

    /**
     * Send a prompt to Ollama and return the raw text response.
     * Returns null if the request fails or times out.
     */
    private function callOllama(string $prompt): ?string
    {
        $payload = json_encode([
            'model'  => $this->ollamaModel,
            'prompt' => $prompt,
            'stream' => false,
        ]);

        $ch = curl_init($this->ollamaUrl . '/api/generate');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Accept: application/json',
            ],
            CURLOPT_TIMEOUT        => $this->timeout,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        $body     = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr  = curl_error($ch);
        curl_close($ch);

        if ($curlErr || $httpCode !== 200 || $body === false) {
            \Log::warning('AiService: cURL error or bad status', [
                'http_code' => $httpCode,
                'curl_error' => $curlErr,
            ]);
            return null;
        }

        $data = json_decode($body, true);
        return $data['response'] ?? null;
    }
}
