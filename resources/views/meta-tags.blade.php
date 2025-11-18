@php
use Shammaa\LaravelSEO\Services\MetaTagsManager;
use Shammaa\LaravelSEO\Services\OpenGraphManager;
use Shammaa\LaravelSEO\Services\TwitterCardManager;
use Shammaa\LaravelSEO\Services\JsonLdManager;
$metaTagsManager = app(MetaTagsManager::class);
$openGraphManager = app(OpenGraphManager::class);
$twitterCardManager = app(TwitterCardManager::class);
$jsonLdManager = app(JsonLdManager::class);
@endphp

{{-- Performance Optimization Tags --}}
{!! $performanceTags ?? '' !!}

{{-- Meta Tags --}}
{!! $metaTagsManager->generate() !!}
{!! $openGraphManager->generate() !!}
{!! $twitterCardManager->generate() !!}
{!! $jsonLdManager->generate() !!}
{{-- LinkedIn uses OpenGraph tags, so no need to generate separately --}}

{{-- AMP Link --}}
@if(isset($ampUrl))
<link rel="amphtml" href="{{ $ampUrl }}">
@endif

{{-- RSS Feed Link --}}
@if(config('seo.rss.enabled') && !empty(config('seo.rss.url')))
<link rel="alternate" type="application/rss+xml" href="{{ config('seo.rss.url') }}">
@endif

{{-- Pagination Links --}}
@if(isset($paginationLinks))
    @if(!empty($paginationLinks['prev']))
    <link rel="prev" href="{{ $paginationLinks['prev'] }}">
    @endif
    @if(!empty($paginationLinks['next']))
    <link rel="next" href="{{ $paginationLinks['next'] }}">
    @endif
@endif

{{-- Analytics Tags --}}
{!! $analyticsTags ?? '' !!}

