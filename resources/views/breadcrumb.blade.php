@php
    $breadcrumbs = $breadcrumbs ?? [];
    $separator = $separator ?? ' / ';
    $class = $class ?? 'breadcrumb';
    $itemClass = $itemClass ?? 'breadcrumb-item';
@endphp

@if(!empty($breadcrumbs))
<nav aria-label="breadcrumb" class="{{ $class }}">
    <ol class="breadcrumb-list" itemscope itemtype="https://schema.org/BreadcrumbList">
        @foreach($breadcrumbs as $index => $item)
            <li class="{{ $itemClass }} {{ $loop->last ? 'active' : '' }}" 
                itemprop="itemListElement" 
                itemscope 
                itemtype="https://schema.org/ListItem">
                @if(isset($item['item']) && !$loop->last)
                    <a href="{{ $item['item'] }}" itemprop="item">
                        <span itemprop="name">{{ $item['name'] }}</span>
                    </a>
                @else
                    <span itemprop="name">{{ $item['name'] }}</span>
                @endif
                <meta itemprop="position" content="{{ $item['position'] }}" />
                @if(!$loop->last)
                    <span class="separator">{{ $separator }}</span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
@endif

