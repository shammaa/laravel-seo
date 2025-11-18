<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    |
    | Cache TTL for site data in seconds (default: 24 hours)
    |
    */
    'cache_ttl' => env('SEO_CACHE_TTL', 86400),

    /*
    |--------------------------------------------------------------------------
    | Site Information
    |--------------------------------------------------------------------------
    |
    | Basic site information used across all SEO tags
    |
    */
    'site' => [
        'name' => env('SEO_SITE_NAME', config('app.name')),
        'description' => env('SEO_SITE_DESCRIPTION', ''),
        'url' => env('SEO_SITE_URL', url('/')),
        'logo' => env('SEO_SITE_LOGO', null), // Path to logo image
        'publisher' => env('SEO_SITE_PUBLISHER', null), // Publisher name (defaults to site name)
        'locale' => env('SEO_SITE_LOCALE', null), // Override locale for og:locale (defaults to app.locale)
    ],

    /*
    |--------------------------------------------------------------------------
    | Image Route Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for image route helper (if using image processing package)
    |
    */
    'image_route' => [
        'name' => env('SEO_IMAGE_ROUTE_NAME', 'image'), // Route name for image processing
        'og_size' => env('SEO_IMAGE_OG_SIZE', '1200x630'), // OpenGraph image size
        'twitter_size' => env('SEO_IMAGE_TWITTER_SIZE', '1200x630'), // Twitter card image size
        'linkedin_size' => env('SEO_IMAGE_LINKEDIN_SIZE', '1200x627'), // LinkedIn image size
        'logo_size' => env('SEO_IMAGE_LOGO_SIZE', '265x85'), // Logo size
    ],

    /*
    |--------------------------------------------------------------------------
    | Image Sizes
    |--------------------------------------------------------------------------
    |
    | Default image sizes for different platforms
    |
    */
    'image_sizes' => [
        'og' => [
            'width' => '1200',
            'height' => '630',
        ],
        'twitter' => [
            'width' => '1200',
            'height' => '630',
        ],
        'linkedin' => [
            'width' => '1200',
            'height' => '627',
        ],
        'schema' => [
            ['width' => 1920, 'height' => 1440],
            ['width' => 1920, 'height' => 1080],
            ['width' => 1800, 'height' => 1800],
            ['width' => 1200, 'height' => 630],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Values
    |--------------------------------------------------------------------------
    |
    | Default values used when data is not available
    |
    */
    'defaults' => [
        'image' => env('SEO_DEFAULT_IMAGE', 'images/default.jpg'),
        'logo' => env('SEO_DEFAULT_LOGO', 'images/default-logo.jpg'),
        'keywords' => env('SEO_DEFAULT_KEYWORDS', []), // Array of default keywords
        'fallbacks' => [
            'post_title' => 'Post',
            'category_name' => 'Category',
            'category_description' => 'Latest news in :name category',
            'search_title' => 'Search results for: :query - :site',
            'search_description' => 'Find news and articles about: :query',
            'search_keyword_prefix' => 'News ',
            'search_keyword' => 'search',
            'product_name' => 'Product',
            'tag_name' => 'Tag',
            'author_name' => 'Author',
            'archive_name' => 'Archive',
            'page_title' => 'Page',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Page Type Configurations
    |--------------------------------------------------------------------------
    |
    | Configuration for different page types
    |
    */
    'pages' => [
        'home' => [
            'title' => null, // null = auto-generate from translation
            'description' => null, // null = use site description
            'image' => null, // null = use site logo
            'schema' => 'WebSite',
            'keywords' => [],
            'author' => null, // null = use site name
            'robots' => 'index, follow',
        ],

        'post' => [
            'title_prefix' => true, // Add site name as suffix
            'description_limit' => 30, // Word limit for description
            'robots' => 'index, follow',
            'author' => null, // null = auto-detect from model
        ],

        'category' => [
            'title_prefix' => true, // Add site name as suffix
            'description_limit' => 30, // Word limit for description
            'author' => null, // null = use site name
            'robots' => 'index, follow',
        ],

        'search' => [
            'title' => 'Search results for: :query - :site',
            'description' => 'Find news and articles about: :query',
            'keywords' => [],
            'author' => null, // null = use site name
            'robots' => 'noindex, follow',
        ],

        'product' => [
            'title_prefix' => true, // Add site name as suffix
            'description_limit' => 30, // Word limit for description
            'robots' => 'index, follow',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Social Media Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for social media platforms
    |
    */
    'social' => [
        'twitter' => [
            'card_type' => env('SEO_TWITTER_CARD_TYPE', 'summary_large_image'), // summary, summary_large_image
            'site' => env('SEO_TWITTER_SITE', null), // @username
            'creator' => env('SEO_TWITTER_CREATOR', null), // @username
        ],

        'facebook' => [
            'app_id' => env('SEO_FACEBOOK_APP_ID', null),
        ],

        'linkedin' => [
            // LinkedIn uses OpenGraph tags, no additional config needed
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Breadcrumb Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for breadcrumb schema
    |
    */
    'breadcrumb' => [
        'home_label' => env('SEO_BREADCRUMB_HOME_LABEL', 'Home'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Organization Schema
    |--------------------------------------------------------------------------
    |
    | Configuration for Organization schema (used in home page)
    |
    */
    'organization' => [
        'name' => env('SEO_ORG_NAME', null),
        'alternate_name' => env('SEO_ORG_ALTERNATE_NAME', null),
        'description' => env('SEO_ORG_DESCRIPTION', null),
        'logo_width' => env('SEO_ORG_LOGO_WIDTH', 265),
        'logo_height' => env('SEO_ORG_LOGO_HEIGHT', 85),
        'same_as' => [
            // Social media profiles
            // 'https://www.facebook.com/yourpage',
            // 'https://twitter.com/yourhandle',
            // 'https://www.instagram.com/yourhandle',
        ],
        'contact_point' => [
            'email' => env('SEO_ORG_EMAIL', null),
            'contact_type' => env('SEO_ORG_CONTACT_TYPE', 'customer service'),
            'available_language' => ['Arabic', 'English'],
            'area_served' => env('SEO_ORG_AREA_SERVED', 'SY'),
        ],
        'address' => [
            'address_country' => env('SEO_ORG_ADDRESS_COUNTRY', 'SY'),
            'address_locality' => env('SEO_ORG_ADDRESS_LOCALITY', 'Damascus'),
        ],
        'founding_date' => env('SEO_ORG_FOUNDING_DATE', null),
        'publishing_principles' => env('SEO_ORG_PUBLISHING_PRINCIPLES', null), // URL to ethics/principles page
    ],

    /*
    |--------------------------------------------------------------------------
    | Multilingual & Hreflang Support
    |--------------------------------------------------------------------------
    |
    | Configuration for multilingual SEO with hreflang tags
    |
    */
    'multilingual' => [
        'enabled' => env('SEO_MULTILINGUAL_ENABLED', false),
        'locales' => env('SEO_MULTILINGUAL_LOCALES', ['ar', 'en']), // Array of supported locales
        'default_locale' => env('SEO_MULTILINGUAL_DEFAULT', 'ar'),
        'x_default' => env('SEO_MULTILINGUAL_X_DEFAULT', true), // Add x-default hreflang
        'url_generator' => null, // Callable: function($locale, $model, $currentUrl) { return $url; }
    ],

    /*
    |--------------------------------------------------------------------------
    | Reading Time Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for article reading time calculation
    |
    */
    'reading_time' => [
        'enabled' => env('SEO_READING_TIME_ENABLED', true),
        'words_per_minute' => env('SEO_READING_TIME_WPM', 200), // Average reading speed
        'translations' => [
            'en' => ':minutes min read',
            'ar' => ':minutes دقيقة قراءة',
            'fr' => ':minutes min de lecture',
            'es' => ':minutes min de lectura',
            'de' => ':minutes Min. Lesezeit',
            'it' => ':minutes min di lettura',
            'pt' => ':minutes min de leitura',
            'ru' => ':minutes мин. чтения',
            'zh' => ':minutes 分钟阅读',
            'ja' => ':minutes 分で読める',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | AMP (Accelerated Mobile Pages) Support
    |--------------------------------------------------------------------------
    |
    | Configuration for AMP pages
    |
    */
    'amp' => [
        'enabled' => env('SEO_AMP_ENABLED', false),
        'url_generator' => null, // Callable: function($model) { return $ampUrl; }
    ],

    /*
    |--------------------------------------------------------------------------
    | RSS/Atom Feeds
    |--------------------------------------------------------------------------
    |
    | Configuration for RSS feed links
    |
    */
    'rss' => [
        'enabled' => env('SEO_RSS_ENABLED', false),
        'url' => env('SEO_RSS_URL', '/feed'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Pagination Support
    |--------------------------------------------------------------------------
    |
    | Configuration for prev/next pagination links
    |
    */
    'pagination' => [
        'enabled' => env('SEO_PAGINATION_ENABLED', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Performance Optimization
    |--------------------------------------------------------------------------
    |
    | DNS Prefetch, Preconnect, and Preload for better performance
    |
    */
    'performance' => [
        'dns_prefetch' => [
            // 'cdn.example.com',
            // 'fonts.googleapis.com',
        ],
        'preconnect' => [
            // 'https://fonts.googleapis.com',
            // 'https://fonts.gstatic.com',
        ],
        'preload' => [
            // [
            //     'href' => '/fonts/main.woff2',
            //     'as' => 'font',
            //     'type' => 'font/woff2',
            // ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Mobile-Specific Tags
    |--------------------------------------------------------------------------
    |
    | Configuration for mobile optimization
    |
    */
    'mobile' => [
        'theme_color' => env('SEO_MOBILE_THEME_COLOR', '#ffffff'),
        'apple_mobile_web_app' => [
            'enabled' => env('SEO_APPLE_MOBILE_WEB_APP', false),
            'status_bar_style' => env('SEO_APPLE_STATUS_BAR_STYLE', 'default'), // default, black, black-translucent
            'title' => env('SEO_APPLE_MOBILE_TITLE', null), // Custom title for iOS
        ],
        'manifest' => env('SEO_MOBILE_MANIFEST', '/manifest.json'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Headers
    |--------------------------------------------------------------------------
    |
    | Configuration for security meta tags
    |
    */
    'security' => [
        'content_security_policy' => env('SEO_CSP', null),
        'referrer_policy' => env('SEO_REFERRER_POLICY', 'strict-origin-when-cross-origin'),
        'x_frame_options' => env('SEO_X_FRAME_OPTIONS', 'SAMEORIGIN'), // DENY, SAMEORIGIN, ALLOW-FROM
        'x_content_type_options' => env('SEO_X_CONTENT_TYPE_OPTIONS', 'nosniff'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Analytics Integration
    |--------------------------------------------------------------------------
    |
    | Configuration for analytics services
    |
    */
    'analytics' => [
        'ga4' => [
            'measurement_id' => env('SEO_GA4_MEASUREMENT_ID', null), // G-XXXXXXXXXX
        ],
        'gtm' => [
            'container_id' => env('SEO_GTM_CONTAINER_ID', null), // GTM-XXXXXXX
        ],
        'yandex' => [
            'counter_id' => env('SEO_YANDEX_COUNTER_ID', null),
        ],
        'facebook' => [
            'pixel_id' => env('SEO_FACEBOOK_PIXEL_ID', null),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Image Rendering Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for image lazy loading and optimization
    |
    */
    'image_rendering' => [
        'loading' => env('SEO_IMAGE_LOADING', 'lazy'), // lazy, eager
        'decoding' => env('SEO_IMAGE_DECODING', 'async'), // async, sync, auto
        'fetchpriority' => env('SEO_IMAGE_FETCHPRIORITY', null), // high, low, auto (for important images)
    ],

    /*
    |--------------------------------------------------------------------------
    | E-commerce Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for e-commerce and product pages
    |
    */
    'ecommerce' => [
        'default_currency' => env('SEO_ECOMMERCE_CURRENCY', 'USD'), // Default currency code
    ],

    /*
    |--------------------------------------------------------------------------
    | Geo-targeting Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for geographic targeting meta tags
    |
    */
    'geo_targeting' => [
        'enabled' => env('SEO_GEO_TARGETING_ENABLED', false),
        'country' => env('SEO_GEO_COUNTRY', null), // Country code (e.g., 'SY')
        'region' => env('SEO_GEO_REGION', null), // Region code (e.g., 'SY-DI' for Damascus)
        'placename' => env('SEO_GEO_PLACENAME', null), // City/Place name
        'latitude' => env('SEO_GEO_LATITUDE', null), // Latitude coordinate
        'longitude' => env('SEO_GEO_LONGITUDE', null), // Longitude coordinate
    ],

    /*
    |--------------------------------------------------------------------------
    | Social Media Configuration (Additional)
    |--------------------------------------------------------------------------
    |
    | Configuration for additional social media platforms
    |
    */
    'social' => [
        'twitter' => [
            'card_type' => env('SEO_TWITTER_CARD_TYPE', 'summary_large_image'),
            'site' => env('SEO_TWITTER_SITE', null),
            'creator' => env('SEO_TWITTER_CREATOR', null),
        ],

        'facebook' => [
            'app_id' => env('SEO_FACEBOOK_APP_ID', null),
        ],

        'linkedin' => [
            // LinkedIn uses OpenGraph tags, no additional config needed
        ],

        'pinterest' => [
            'verify' => env('SEO_PINTEREST_VERIFY', null), // Pinterest site verification code
        ],

        'whatsapp' => [
            'enabled' => env('SEO_WHATSAPP_ENABLED', true), // WhatsApp uses OpenGraph
        ],

        'telegram' => [
            'enabled' => env('SEO_TELEGRAM_ENABLED', true), // Telegram uses OpenGraph + Twitter Cards
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Performance Optimization (Enhanced)
    |--------------------------------------------------------------------------
    |
    | Additional performance hints: prefetch, prerender, modulepreload
    |
    */
    'performance' => [
        'dns_prefetch' => [
            // 'cdn.example.com',
            // 'fonts.googleapis.com',
        ],
        'preconnect' => [
            // 'https://fonts.googleapis.com',
            // 'https://fonts.gstatic.com',
        ],
        'preload' => [
            // [
            //     'href' => '/fonts/main.woff2',
            //     'as' => 'font',
            //     'type' => 'font/woff2',
            //     'onload' => "this.onload=null;this.rel='stylesheet'", // For critical CSS
            // ],
        ],
        'prefetch' => [
            // URLs to prefetch (for next likely pages)
            // '/next-page',
        ],
        'prerender' => [
            // URLs to prerender (for important pages)
            // '/important-page',
        ],
        'modulepreload' => [
            // ES modules to preload
            // [
            //     'href' => '/js/app.js',
            //     'type' => 'module',
            // ],
        ],
    ],
];

