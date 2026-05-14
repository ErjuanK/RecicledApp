<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Services\LastFmService;
use App\Services\ItunesService;

class RefreshEditorialReleases extends Command
{
    protected $signature   = 'releases:refresh {--force : Bypass cache and force a fresh fetch}';
    protected $description = 'Fetches the weekly top albums from Last.fm + enriches them with iTunes data and stores the result as the editorial feed.';

    private LastFmService $lastFm;
    private ItunesService $itunes;

    public function __construct(LastFmService $lastFm, ItunesService $itunes)
    {
        parent::__construct();
        $this->lastFm = $lastFm;
        $this->itunes = $itunes;
    }

    public function handle(): int
    {
        $this->info('🎵 Refreshing weekly editorial releases...');

        if ($this->option('force')) {
            // Clear tag album caches so Last.fm returns fresh data
            Cache::forget('editorial.releases.weekly');
            foreach (['latin', 'pop', 'hip-hop', 'indie', 'urban', 'r&b'] as $tag) {
                Cache::forget("lastfm.tag.albums." . md5($tag . '3'));
            }
            $this->warn('  Cache cleared — fetching fresh data.');
        }

        // 1. Pull the pool from Last.fm
        $this->line('  → Fetching top albums from Last.fm...');
        $pool = $this->lastFm->getWeeklyEditorialPool(perTag: 3);

        if (empty($pool)) {
            $this->error('  Last.fm returned no results. Aborting.');
            return self::FAILURE;
        }

        $this->line("  → Last.fm returned " . count($pool) . " candidates.");

        // 2. Enrich each album with iTunes cover + meta, take the first 6 that resolve
        $editorial = [];
        $types = [
            'Álbum de Estudio', 'Álbum', 'Álbum', 'EP', 'EP', 'Álbum',
        ];

        $bar = $this->output->createProgressBar(min(count($pool), 12));
        $bar->start();

        foreach ($pool as $candidate) {
            if (count($editorial) >= 6) break;

            $artist = $candidate['artist'];
            $title  = $candidate['title'];

            // Skip obviously bad data
            if (strlen($artist) < 2 || strlen($title) < 2) {
                $bar->advance();
                continue;
            }

            $coverUrl = $this->itunes->getCoverUrl($artist, $title);

            if ($coverUrl) {
                $typeIndex = count($editorial) % count($types);
                $editorial[] = [
                    'title'         => $title,
                    'artist'        => $artist,
                    'itunes_artist' => $artist,
                    'itunes_album'  => $title,
                    'date'          => now()->translatedFormat('d \d\e F'),
                    'type'          => $types[$typeIndex],
                    'description'   => "Uno de los álbumes más escuchados de la semana según Last.fm. \"{$title}\" de {$artist} acumula miles de escuchas globales.",
                    'cover_url'     => $coverUrl,
                ];
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        if (empty($editorial)) {
            $this->error('  No albums could be enriched with iTunes data. Aborting.');
            return self::FAILURE;
        }

        // 3. Store in cache for 7 days (overwritten every Monday by the scheduler)
        Cache::put('editorial.releases.weekly', $editorial, now()->addDays(7));

        $this->info("  ✅ Stored " . count($editorial) . " editorial releases in cache.");
        foreach ($editorial as $e) {
            $this->line("     • {$e['artist']} — {$e['title']}");
        }

        Log::info('RefreshEditorialReleases: Updated with ' . count($editorial) . ' albums.');

        return self::SUCCESS;
    }
}
