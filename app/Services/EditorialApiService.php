<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class EditorialApiService
{
    /**
     * Returns curated recent releases.
     * Priority: 1) Weekly cache from Last.fm (populated by releases:refresh command)
     *           2) Hardcoded fallback list (so the page never breaks)
     */
    public function getRecentEditorialReleases(): array
    {
        // If the weekly automated refresh has run, use it
        $cached = Cache::get('editorial.releases.weekly');
        if (!empty($cached)) {
            return $cached;
        }

        // Fallback: the hardcoded editorial list
        return $this->getHardcodedReleases();
    }

    private function getHardcodedReleases(): array
    {
        return [
            [
                'title'         => 'El Baifo',
                'artist'        => 'Quevedo',
                'itunes_artist' => 'Quevedo',
                'itunes_album'  => 'El Baifo',
                'date'          => 'Finales de abril',
                'type'          => 'Álbum de Estudio',
                'description'   => 'El esperado álbum de estudio del artista urbano español. El disco ha dado mucho de qué hablar gracias a colaboraciones llamativas, como su tema junto al legendario Elvis Crespo.',
            ],
            [
                'title'         => 'Antes que el tiempo se vaya',
                'artist'        => 'Fonseca',
                'itunes_artist' => 'Fonseca',
                'itunes_album'  => 'Antes que el tiempo se vaya',
                'date'          => '26 de abril',
                'type'          => 'Álbum',
                'description'   => 'El cantautor colombiano regresó con un disco muy íntimo, honesto y conectado a sus raíces. Incluye colaboraciones de lujo con figuras como Juanes, Rubén Blades, Manuel Medrano y Nanpa Básico.',
            ],
            [
                'title'         => 'Destiempo',
                'artist'        => 'Erick Brian',
                'itunes_artist' => 'Erick Brian Colon',
                'itunes_album'  => 'Destiempo',
                'date'          => '26 de abril',
                'type'          => 'Álbum',
                'description'   => 'El artista cubano (exintegrante de CNCO) estrenó esta nueva propuesta enfocada en el pop urbano, en la cual incluye temas en solitario y colaboraciones con antiguos compañeros.',
            ],
            [
                'title'         => 'If These Walls Could Talk',
                'artist'        => 'Jorge Mejía',
                'itunes_artist' => 'Jorge Mejia',
                'itunes_album'  => 'If These Walls Could Talk',
                'date'          => '24 de abril',
                'type'          => 'Álbum Clásico',
                'description'   => 'El pianista y compositor colombo-estadounidense lanzó este disco clásico grabado en los estudios Abbey Road junto a la Orquesta Sinfónica de Londres.',
            ],
            [
                'title'         => 'Vallenato',
                'artist'        => 'La Banda del 5',
                'itunes_artist' => 'La Banda del 5',
                'itunes_album'  => 'Vallenato',
                'date'          => 'Reciente',
                'type'          => 'EP',
                'description'   => 'La agrupación colombiana reafirma su identidad con este EP de seis canciones, consolidando su estilo "bandanato".',
            ],
            [
                'title'         => 'A Corazón Abierto',
                'artist'        => 'Andrés Cortés',
                'itunes_artist' => 'Andres Cortes',
                'itunes_album'  => 'A Corazon Abierto',
                'date'          => 'Reciente',
                'type'          => 'EP',
                'description'   => 'Una producción de regional colombiano donde el artista rinde homenaje a clásicos que marcaron su identidad musical.',
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
