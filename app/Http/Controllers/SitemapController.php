<?php

namespace App\Http\Controllers;

use App\Models\Business;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function sitemap(): Response
    {
        $urls = collect([
            ['loc' => url('/'), 'priority' => '1.0'],
            ['loc' => route('directory'), 'priority' => '0.8'],
            ['loc' => route('help'), 'priority' => '0.4'],
            ['loc' => route('legal.privacy'), 'priority' => '0.3'],
            ['loc' => route('legal.terms'), 'priority' => '0.3'],
        ]);

        foreach (config('nexo.categories') as $category) {
            $urls->push([
                'loc' => route('directory.category', $category),
                'priority' => '0.6',
            ]);
        }

        Business::query()
            ->where('in_directory', true)
            ->orderBy('id')
            ->get(['slug', 'updated_at'])
            ->each(function (Business $business) use ($urls) {
                $urls->push([
                    'loc' => route('public.business', $business->slug),
                    'lastmod' => $business->updated_at?->toAtomString(),
                    'priority' => '0.7',
                ]);
            });

        return response()
            ->view('sitemap', ['urls' => $urls])
            ->header('Content-Type', 'application/xml');
    }

    public function robots(): Response
    {
        $lines = [
            'User-agent: *',
            'Allow: /$',
            'Allow: /explorar',
            'Disallow: /app',
            'Disallow: /t/',
            'Disallow: /login',
            'Disallow: /register',
            'Disallow: /forgot-password',
            'Disallow: /reset-password',
            '',
            'Sitemap: '.url('/sitemap.xml'),
        ];

        return response(implode("\n", $lines)."\n")
            ->header('Content-Type', 'text/plain');
    }
}
