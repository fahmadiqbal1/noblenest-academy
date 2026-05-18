{{-- Phase 6 — schema.org structured data for the marketing surface. --}}
<script type="application/ld+json">
{{-- '@'.'context' / '@'.'type': the literal @context/@type tokens are
     parsed by Blade as the @context / @php-style directives and leak raw
     PHP into the JSON-LD. Splitting the token keeps Blade out while
     json_encode still emits the correct "@context"/"@type" keys. --}}
{!! json_encode([
    '@'.'context' => 'https://schema.org',
    '@'.'type'    => 'EducationalOrganization',
    'name'     => 'Noble Nest Global Academy',
    'url'      => config('app.url') ?? url('/'),
    'logo'     => asset('brand/noblenest-logo.svg'),
    'description' => 'Family-first online learning academy for children ages 0–10 — multilingual, accessibility-first, AI-supported.',
    'sameAs'   => array_values(array_filter([
        env('SOCIAL_TWITTER_URL'),
        env('SOCIAL_LINKEDIN_URL'),
        env('SOCIAL_INSTAGRAM_URL'),
        env('SOCIAL_YOUTUBE_URL'),
    ])),
    'inLanguage' => ['en', 'fr', 'ru', 'zh', 'es', 'ko', 'ur', 'ar'],
    'audience'   => [
        '@'.'type'       => 'PeopleAudience',
        'suggestedMinAge' => 0,
        'suggestedMaxAge' => 10,
    ],
], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>
