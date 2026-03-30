<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiService
{
    private string $ollamaUrl;
    private string $ollamaModel;

    public function __construct()
    {
        $this->ollamaUrl = env('OLLAMA_URL', 'http://localhost:11434');
        $this->ollamaModel = env('OLLAMA_MODEL', 'gemma');
    }

    public function getRefinedSeed($likes, $dislikes) 
    {
        // If there's no history, we return empty so the controller can fallback
        if ($likes->isEmpty() && $dislikes->isEmpty()) {
            Log::info("AiService: No likes or dislikes provided, returning empty array.");
            return [];
        }

        $likesStr = $likes->implode('spotify_track_id', ', '); // In a real app we'd fetch actual track names or use our db track info, but for now we send raw context
        $dislikesStr = $dislikes->implode('spotify_track_id', ', ');

        // It is better to have genre or artist names, but we rely on the prompt context
        $prompt = "Eres un algoritmo de recomendación musical. 
        Al usuario LE GUSTAN estas interacciones (IDs Spotify u otros): [$likesStr]. 
        Al usuario NO LE GUSTAN estas: [$dislikesStr].
        
        Analiza el patrón (ritmo, instrumentos, energía) que crees que representan. 
        Devuélveme una lista de 3 géneros o estilos musicales (en inglés, estándar de Spotify) que encajen con lo que le gusta pero se alejen de lo que rechazó. 
        Responde SOLO con los términos separados por comas (ej. pop, rock, jazz).";

        try {
            $response = Http::timeout(10)->post("{$this->ollamaUrl}/api/generate", [
                'model' => $this->ollamaModel,
                'prompt' => $prompt,
                'stream' => false
            ]);

            if ($response->successful()) {
                $rawText = $response->json()['response'] ?? '';
                // Clean up any extra text
                $cleaned = str_replace(["\n", "\r", ".", " "], "", $rawText);
                $genres = explode(',', $cleaned);
                return array_filter($genres);
            }
            
            Log::warning("AiService: Ollama API returned non-success", ['status' => $response->status()]);
            return [];

        } catch (\Exception $e) {
            Log::error("AiService: Exception when calling Ollama API", ['error' => $e->getMessage()]);
            return [];
        }
    }
}
