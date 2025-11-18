@php
use Shammaa\LaravelSEO\Facades\SEO;
use Shammaa\LaravelSEO\Facades\OG;
use Shammaa\LaravelSEO\Facades\Twitter;
use Shammaa\LaravelSEO\Facades\Schema;
@endphp

{{-- Performance Optimization Tags --}}
{!! $performanceTags ?? '' !!}

{{-- Meta Tags --}}
{!! SEO::generate() !!}
{!! OG::generate() !!}
{!! Twitter::generate() !!}
{!! Schema::generate() !!}
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

