<?php

namespace App\Services;

class EditorialApiService
{
    /**
     * Returns curated recent releases (Álbumes Destacados & EPs Recientes).
     */
    public function getRecentEditorialReleases(): array
    {
        return [
            [
                'title' => 'El Baifo',
                'artist' => 'Quevedo',
                'date' => 'Finales de abril',
                'type' => 'Álbum de Estudio',
                'description' => 'El esperado álbum de estudio del artista urbano español. El disco ha dado mucho de qué hablar gracias a colaboraciones llamativas, como su tema junto al legendario Elvis Crespo.',
                'spotify_id' => '7a8QhNYgKmcauIKB7rCyR5'
            ],
            [
                'title' => 'Antes que el tiempo se vaya',
                'artist' => 'Fonseca',
                'date' => '26 de abril',
                'type' => 'Álbum',
                'description' => 'El cantautor colombiano regresó con un disco muy íntimo, honesto y conectado a sus raíces. Incluye colaboraciones de lujo con figuras como Juanes, Rubén Blades, Manuel Medrano y Nanpa Básico.',
                'spotify_id' => '0mQ8W2ZntO6iWlQp1GZ3H9' // Dummy ID
            ],
            [
                'title' => 'Destiempo',
                'artist' => 'Erick Brian',
                'date' => '26 de abril',
                'type' => 'Álbum',
                'description' => 'El artista cubano (exintegrante de CNCO) estrenó esta nueva propuesta enfocada en el pop urbano, en la cual incluye temas en solitario y colaboraciones con antiguos compañeros como Christopher Vélez.',
                'spotify_id' => '1t2Y3Z4A5B6C7D8E9F0G1H' // Dummy ID
            ],
            [
                'title' => 'If These Walls Could Talk',
                'artist' => 'Jorge Mejía',
                'date' => '24 de abril',
                'type' => 'Álbum Clásico',
                'description' => 'El pianista, compositor y reconocido ejecutivo musical colombo-estadounidense lanzó este disco clásico grabado nada menos que en los estudios Abbey Road junto a la Orquesta Sinfónica de Londres.',
                'spotify_id' => '2x3Y4Z5A6B7C8D9E0F1G2H' // Dummy ID
            ],
            [
                'title' => 'Vallenato',
                'artist' => 'La Banda del 5',
                'date' => 'Reciente',
                'type' => 'EP',
                'description' => 'La agrupación colombiana reafirma su identidad con este EP de seis canciones, consolidando su estilo "bandanato" (una mezcla de la raíz del vallenato tradicional con elementos de calle contemporáneos).',
                'spotify_id' => '3w4X5Y6Z7A8B9C0D1E2F3G' // Dummy ID
            ],
            [
                'title' => 'A Corazón Abierto',
                'artist' => 'Andrés Cortés',
                'date' => 'Reciente',
                'type' => 'EP',
                'description' => 'Una producción de regional colombiano donde el artista rinde homenaje a clásicos que marcaron su identidad musical, reinterpretando temas de leyendas como Omar Geles y Patricia Teherán.',
                'spotify_id' => '4v5W6X7Y8Z9A0B1C2D3E4F' // Dummy ID
            ],
        ];
    }

    /**
     * Returns a list of strictly upcoming releases.
     */
    public function getUpcomingReleases(): array
    {
        return [
            [
                'title' => 'Odisea',
                'artist' => 'Los Retros',
                'date' => 'Próximamente (Mayo)',
                'type' => 'Álbum Debut',
                'description' => 'Es el esperado álbum debut del proyecto liderado por el mexicoamericano Mauri Tapia. El disco está fuertemente inspirado en el City Pop japonés de los años 80 y el jazz fusión.',
            ],
            [
                'title' => 'Punto de Inflexión',
                'artist' => 'Fer Franco',
                'date' => 'Próximamente',
                'type' => 'Álbum',
                'description' => 'El artista guatemalteco presentó este álbum enfocado en la introspección personal y la transformación a través de ritmos alternativos.',
            ],
        ];
    }

    /**
     * Optional: Return a curated description for a specific album if we have one on file.
     */
    public function getCuratedDescription(string $albumName, string $artistName): ?string
    {
        // Simple mock matching logic for demonstration
        $curated = [
            'Radical Optimism' => 'El esperadísimo regreso de Dua Lipa con sonidos psicodélicos y pop de los años 70.',
            'HIT ME HARD AND SOFT' => 'Billie Eilish explora nuevas dimensiones vocales y de producción junto a FINNEAS en su tercer álbum de estudio.',
        ];

        foreach ($curated as $key => $desc) {
            if (stripos($albumName, $key) !== false) {
                return $desc;
            }
        }
        
        return null;
    }
}
