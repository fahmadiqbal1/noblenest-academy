<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

/**
 * Phase 6 — sitemap.xml endpoint.
 *
 * Lists every public URL the marketing surface should expose, plus hreflang
 * alternates for the 8 supported locales. Cached for 1 hour to avoid
 * recomputing on every crawl.
 */
class SitemapController extends Controller
{
    private const LOCALES = ['en', 'fr', 'ru', 'zh', 'es', 'ko', 'ur', 'ar'];

    public function __invoke(): Response
    {
        $xml = Cache::remember('sitemap.xml', 3600, fn () => $this->build());

        return response($xml, 200, ['Content-Type' => 'application/xml']);
    }

    private function build(): string
    {
        $base = config('app.url') ?: 'http://localhost';
        $urls = [
            ['loc' => '/',          'priority' => '1.0', 'change' => 'weekly'],
            ['loc' => '/pricing',   'priority' => '0.9', 'change' => 'monthly'],
            ['loc' => '/login',     'priority' => '0.6', 'change' => 'yearly'],
            ['loc' => '/register',  'priority' => '0.8', 'change' => 'yearly'],
            ['loc' => '/terms',     'priority' => '0.3', 'change' => 'yearly'],
            ['loc' => '/privacy',   'priority' => '0.3', 'change' => 'yearly'],
        ];

        $out = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $out .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">'."\n";
        foreach ($urls as $u) {
            $loc = rtrim($base, '/').$u['loc'];
            $out .= "  <url>\n";
            $out .= '    <loc>'.htmlspecialchars($loc, ENT_XML1 | ENT_QUOTES, 'UTF-8')."</loc>\n";
            $out .= "    <changefreq>{$u['change']}</changefreq>\n";
            $out .= "    <priority>{$u['priority']}</priority>\n";
            foreach (self::LOCALES as $locale) {
                $alt = $locale === 'en' ? $loc : rtrim($base, '/')."/lang/{$locale}";
                $out .= "    <xhtml:link rel=\"alternate\" hreflang=\"{$locale}\" href=\"".htmlspecialchars($alt, ENT_XML1 | ENT_QUOTES, 'UTF-8')."\"/>\n";
            }
            $out .= "  </url>\n";
        }
        $out .= '</urlset>'."\n";

        return $out;
    }
}
