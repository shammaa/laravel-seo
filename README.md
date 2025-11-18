# Laravel SEO Package

Professional SEO package for Laravel with comprehensive support for OpenGraph, Twitter Cards, LinkedIn, Schema.org structured data, multilingual SEO, performance optimization, analytics integration, and much more.

## Table of Contents

- [Features](#features)
- [Installation](#installation)
- [Configuration](#configuration)
- [Quick Start](#quick-start)
- [Usage Guide](#usage-guide)
- [Model Integration](#model-integration)
- [Complete Guide](#complete-guide) - **Full Controller & Model Examples**
- [Advanced Features](#advanced-features)
- [Configuration Reference](#configuration-reference)
- [API Reference](#api-reference)
- [Examples](#examples)
- [Troubleshooting](#troubleshooting)
- [Best Practices](#best-practices)
- [Requirements](#requirements)
- [License](#license)

## Features

### Core SEO Features
- ✅ **Meta Tags** - Title, Description, Keywords, Robots, Canonical
- ✅ **OpenGraph Tags** - Complete Facebook sharing support
- ✅ **Twitter Cards** - Summary and large image cards with reading time
- ✅ **LinkedIn Cards** - OpenGraph compatible
- ✅ **Article Tags** - Automatic article:tag generation from model relationships

### Schema.org Structured Data (JSON-LD) - 22+ Types
**Core Schemas:**
- ✅ **NewsArticle** - For blog posts and articles with author, publisher, dates
- ✅ **Product** - For e-commerce products with price, offers, ratings, shipping
- ✅ **Offer** - Enhanced with shipping details and return policy
- ✅ **AggregateRating** - For product ratings from multiple reviews
- ✅ **Brand** - For product brands
- ✅ **WebPage** - For all page types
- ✅ **BreadcrumbList** - Automatic breadcrumb navigation
- ✅ **VideoObject** - Enhanced with duration, contentUrl, interaction statistics
- ✅ **WebSite** - For homepage with search action
- ✅ **Organization** - Complete organization schema
- ✅ **CollectionPage** - For category pages
- ✅ **FAQPage** - For articles with frequently asked questions
- ✅ **HowTo** - For tutorial and instructional articles
- ✅ **Review** - For product and service reviews with ratings
- ✅ **Event** - For event announcements and coverage

**New Advanced Schemas:**
- ✅ **Course** - For educational courses with provider, instances, ratings
- ✅ **Recipe** - For recipes with ingredients, instructions, nutrition info
- ✅ **JobPosting** - For job listings with salary, location, requirements
- ✅ **LocalBusiness** - For local businesses with address, geo, hours
- ✅ **SoftwareApplication** - For apps with ratings, OS, pricing
- ✅ **Book** - For books with ISBN, author, publisher
- ✅ **Movie** - For movies with cast, director, ratings
- ✅ **Podcast** - For podcasts with episodes, author, publisher

### Advanced Features
- ✅ **Multilingual Support** - Hreflang tags for multiple languages
- ✅ **Reading Time** - Automatic calculation and display in Twitter Cards and Schema
- ✅ **AMP Support** - Automatic AMP link generation
- ✅ **RSS/Atom Feeds** - Feed link support
- ✅ **Pagination** - Prev/Next link support
- ✅ **Performance Optimization** - DNS Prefetch, Preconnect, Preload, Prefetch, Prerender, Modulepreload
- ✅ **Mobile Optimization** - Theme color, Apple mobile web app, manifest
- ✅ **Security Headers** - CSP, Referrer Policy, X-Frame-Options
- ✅ **Analytics Integration** - Google Analytics 4, GTM, Yandex Metrica, Facebook Pixel
- ✅ **Image Optimization** - Lazy loading configuration support
- ✅ **Geo-targeting** - Geographic meta tags for location-based SEO
- ✅ **Social Media** - Pinterest Rich Pins, WhatsApp, Telegram optimization
- ✅ **Commands** - `seo:test-schema` and `seo:health-check` for validation

### Developer Experience
- ✅ **Easy Facade API** - Simple, fluent interface
- ✅ **Fully Configurable** - Comprehensive config file
- ✅ **Automatic Detection** - Smart model attribute detection
- ✅ **Model Trait** - Automatic SEO data extraction from models
- ✅ **Extensible** - Easy to add custom schemas

## Installation

### Step 1: Install via Composer

```bash
composer require shammaa/laravel-seo
```

### Step 2: Publish Configuration

```bash
php artisan vendor:publish --tag=seo-config
```

This creates `config/seo.php` where you can configure all SEO settings.

### Step 3: Configure Basic Settings

Edit `config/seo.php` and set your site information:

```php
'site' => [
    'name' => 'Your Site Name',
    'description' => 'Your site description',
    'url' => 'https://yoursite.com',
    'logo' => 'path/to/logo.jpg',
],
```

## Quick Start

### Basic Usage

In your controller:

```php
use Shammaa\LaravelSEO\Facades\SEO;

public function show(Post $post)
{
    SEO::post($post)->set();
    return view('post.show', compact('post'));
}
```

In your Blade layout (`resources/views/layouts/app.blade.php`):

```blade
<head>
    {!! SEO::render() !!}
    {!! $customSchemas ?? '' !!}
</head>
```

**That's it!** The package automatically generates all SEO tags.

## Usage Guide

### Page Types

#### Home Page

```php
public function index()
{
    SEO::home()->set();
    return view('home');
}
```

#### Post/Article Page

```php
public function show(Post $post)
{
    SEO::post($post)->set();
    return view('post.show', compact('post'));
}
```

#### Category Page

```php
public function show(Category $category)
{
    SEO::category($category)->set();
    return view('category.show', compact('category'));
}
```

#### Product Page (E-commerce)

```php
public function show(Product $product)
{
    SEO::product($product)->set();
    return view('product.show', compact('product'));
}
```

**Product Schema automatically includes:**
- Product name, description, images
- SKU, MPN, GTIN
- Brand information
- Price and offers
- Availability status
- Aggregate ratings (from reviews)
- Product properties (color, size, material, etc.)

#### Search Page

```php
public function search(Request $request)
{
    SEO::search(['query' => $request->get('q')])->set();
    return view('search', ['query' => $request->get('q')]);
}
```

#### Tag Page

```php
public function show(Tag $tag)
{
    SEO::for('tag', $tag)->set();
    return view('tag.show', compact('tag'));
}
```

#### Author Page

```php
public function show(Author $author)
{
    SEO::for('author', $author)->set();
    return view('author.show', compact('author'));
}
```

#### Archive Page

```php
public function archive(string $date)
{
    SEO::for('archive', $date)->set(); // $date can be string like "2024-01" or object
    return view('archive.show', compact('date'));
}
```

#### Static Page

```php
public function show(Page $page)
{
    SEO::for('page', $page)->set();
    return view('page.show', compact('page'));
}
```

#### Custom Page Type

```php
public function show(CustomModel $model)
{
    SEO::for('custom', $model)->set();
    return view('custom.show', compact('model'));
}
```

### Advanced Schema Usage

#### FAQ Schema

For articles with frequently asked questions:

```php
SEO::post($article)->set()->addFAQ([
    [
        'question' => 'What is this about?',
        'answer' => 'This article explains...'
    ],
    [
        'question' => 'How does it work?',
        'answer' => 'It works by...'
    ],
]);
```

**From Database (with HasSEO trait):**
```php
// In your Model
class Post extends Model
{
    use HasSEO;
    
    public function faqs()
    {
        return $this->hasMany(FAQ::class);
    }
}

// In Controller - Automatic!
SEO::post($post)->set(); // Automatically detects and adds FAQs
```

#### HowTo Schema

For tutorial and instructional articles:

**Simple Steps:**
```php
SEO::post($tutorial)->set()->addHowTo(
    name: 'How to Cook Kabsa',
    steps: [
        'Wash the rice thoroughly',
        'Cook the meat with spices',
        'Mix rice with meat',
        'Cook on low heat for 30 minutes'
    ],
    description: 'A simple guide to cooking traditional Kabsa',
    image: '/images/kabsa.jpg'
);
```

**Detailed Steps:**
```php
SEO::post($tutorial)->set()->addHowTo(
    name: 'How to Build a Website',
    steps: [
        [
            'name' => 'Choose a Domain',
            'text' => 'Select and register your domain name',
            'image' => '/images/step1.jpg',
            'url' => '/steps/1'
        ],
        [
            'name' => 'Set Up Hosting',
            'text' => 'Choose a hosting provider and set up your account',
            'image' => '/images/step2.jpg'
        ],
    ]
);
```

**From Database (with HasSEO trait):**
```php
// In your Model
class Post extends Model
{
    use HasSEO;
    
    public function steps()
    {
        return $this->hasMany(TutorialStep::class)->orderBy('order');
    }
}

// In Controller - Automatic!
SEO::post($post)->set(); // Automatically detects and adds HowTo steps
```

#### Review Schema

For product and service reviews:

```php
SEO::post($review)->set()->addReview(
    itemName: 'iPhone 15',
    ratingValue: 4.5,
    bestRating: 5.0,
    reviewBody: 'A comprehensive review of the iPhone 15...',
    authorName: 'John Doe',
    datePublished: '2024-01-15'
);
```

**From Database (with HasSEO trait):**
```php
// In your Model
class Post extends Model
{
    use HasSEO;
    
    public function review()
    {
        return $this->hasOne(Review::class);
    }
}

// In Controller - Automatic!
SEO::post($post)->set(); // Automatically detects and adds Review
```

#### Event Schema

For event announcements and coverage:

```php
SEO::post($event)->set()->addEvent(
    name: 'Tech Conference 2024',
    startDate: '2024-01-15T10:00:00+00:00',
    endDate: '2024-01-15T18:00:00+00:00',
    description: 'A major technology conference...',
    locationName: 'Conference Hall',
    locationAddress: 'Damascus, Syria',
    image: '/images/conference.jpg',
    organizerName: 'Tech Company',
    organizerUrl: 'https://tech-company.com'
);
```

**From Database (with HasSEO trait):**
```php
// In your Model
class Post extends Model
{
    use HasSEO;
    
    public function event()
    {
        return $this->hasOne(Event::class);
    }
}

// In Controller - Automatic!
SEO::post($post)->set(); // Automatically detects and adds Event
```

### Breadcrumb Usage

The breadcrumb is automatically generated for `post` and `category` page types.

#### Method 1: Using the shared variable (Automatic)

After calling `SEO::set()`, breadcrumb items are automatically shared with the view:

```blade
@if(isset($breadcrumbs))
    @include('seo::breadcrumb')
@endif
```

#### Method 2: Using the Facade method

```blade
@php
    $breadcrumbs = SEO::post($post)->breadcrumb();
@endphp

@foreach($breadcrumbs as $item)
    @if(isset($item['item']) && !$loop->last)
        <a href="{{ $item['item'] }}">{{ $item['name'] }}</a>
    @else
        <span>{{ $item['name'] }}</span>
    @endif
    @if(!$loop->last) / @endif
@endforeach
```

#### Method 3: Using the included view component

```blade
@include('seo::breadcrumb', [
    'separator' => ' / ',
    'class' => 'breadcrumb',
    'itemClass' => 'breadcrumb-item'
])
```

The breadcrumb view includes Schema.org microdata for SEO.

### Chaining Multiple Schemas

You can chain multiple schema types for a single page:

```php
SEO::post($article)
    ->set()
    ->addFAQ([...])
    ->addHowTo(...)
    ->addReview(...)
    ->addEvent(...);
```

## New Advanced Schemas - Complete Guide

### Course Schema

Perfect for educational websites, online courses, and training platforms:

```php
SEO::addCourse([
    'name' => 'Laravel Advanced Techniques',
    'description' => 'Learn advanced Laravel concepts and best practices',
    'provider' => [
        'name' => 'Tech Academy',
        'url' => 'https://tech-academy.com'
    ],
    'courseCode' => 'LAR-201',
    'educationalLevel' => 'Advanced',
    'inLanguage' => 'ar',
    'image' => '/images/course.jpg',
    'hasCourseInstance' => [
        'startDate' => '2024-02-01',
        'endDate' => '2024-04-30',
        'courseMode' => 'online',
        'instructor' => [
            'name' => 'Ahmed Ali',
            'email' => 'ahmed@example.com'
        ],
        'location' => 'Online Platform'
    ],
    'aggregateRating' => [
        'ratingValue' => 4.8,
        'ratingCount' => 150
    ]
]);
```

### Recipe Schema

Ideal for food blogs, recipe websites, and cooking platforms:

```php
SEO::addRecipe([
    'name' => 'Traditional Kabsa',
    'description' => 'Authentic Saudi Kabsa recipe with step-by-step instructions',
    'image' => '/images/kabsa.jpg',
    'prepTime' => 'PT30M', // ISO 8601 duration format
    'cookTime' => 'PT1H',
    'totalTime' => 'PT1H30M',
    'recipeYield' => '6 servings',
    'recipeCategory' => 'Main Course',
    'recipeCuisine' => 'Saudi',
    'recipeIngredient' => [
        '2 cups basmati rice',
        '1 kg chicken',
        'Kabsa spices',
        'Onions and tomatoes'
    ],
    'recipeInstructions' => [
        'Wash and soak rice for 30 minutes',
        'Cook chicken with spices',
        'Add rice and cook on low heat',
        'Serve hot with salad'
    ],
    'author' => 'Chef Fatima',
    'datePublished' => '2024-01-15',
    'nutrition' => [
        'calories' => '450',
        'fatContent' => '15g',
        'proteinContent' => '30g',
        'carbohydrateContent' => '50g'
    ],
    'aggregateRating' => [
        'ratingValue' => 4.9,
        'ratingCount' => 89
    ]
]);
```

### JobPosting Schema

Perfect for job boards and recruitment websites:

```php
SEO::addJobPosting([
    'title' => 'Senior Laravel Developer',
    'description' => 'We are looking for an experienced Laravel developer...',
    'datePosted' => '2024-01-15',
    'validThrough' => '2024-03-15',
    'employmentType' => ['FULL_TIME', 'CONTRACTOR'],
    'hiringOrganization' => [
        'name' => 'Tech Company',
        'sameAs' => 'https://tech-company.com',
        'logo' => 'https://tech-company.com/logo.png'
    ],
    'jobLocation' => [
        'address' => [
            'streetAddress' => '123 Main Street',
            'addressLocality' => 'Damascus',
            'addressRegion' => 'Damascus',
            'postalCode' => '12345',
            'addressCountry' => 'SY'
        ]
    ],
    'baseSalary' => [
        'currency' => 'USD',
        'value' => [
            'minValue' => 50000,
            'maxValue' => 80000
        ]
    ],
    'jobBenefits' => ['Health Insurance', 'Remote Work', 'Flexible Hours'],
    'qualifications' => [
        'Bachelor\'s degree in Computer Science',
        '5+ years Laravel experience'
    ],
    'skills' => ['Laravel', 'PHP', 'MySQL', 'Vue.js']
]);
```

### LocalBusiness Schema

Great for local businesses, restaurants, shops, and service providers:

```php
SEO::addLocalBusiness([
    'businessType' => 'Restaurant', // or 'LocalBusiness', 'Store', etc.
    'name' => 'Al-Sham Restaurant',
    'description' => 'Authentic Syrian cuisine in the heart of Damascus',
    'address' => [
        'streetAddress' => 'Al-Maliki Street',
        'addressLocality' => 'Damascus',
        'addressRegion' => 'Damascus',
        'postalCode' => '12345',
        'addressCountry' => 'SY'
    ],
    'geo' => [
        'latitude' => 33.5138,
        'longitude' => 36.2765
    ],
    'telephone' => '+963-11-1234567',
    'email' => 'info@alsham-restaurant.com',
    'url' => 'https://alsham-restaurant.com',
    'logo' => '/images/logo.png',
    'image' => ['/images/interior1.jpg', '/images/interior2.jpg'],
    'openingHours' => [
        'Mo-Fr 10:00-22:00',
        'Sa-Su 12:00-23:00'
    ],
    'priceRange' => '$$',
    'paymentAccepted' => ['Cash', 'Credit Card', 'Mobile Payment'],
    'servesCuisine' => ['Syrian', 'Middle Eastern'],
    'menu' => 'https://alsham-restaurant.com/menu',
    'aggregateRating' => [
        'ratingValue' => 4.7,
        'ratingCount' => 234
    ]
]);
```

### SoftwareApplication Schema

Perfect for app stores, software marketplaces, and SaaS platforms:

```php
SEO::addSoftwareApplication([
    'name' => 'My Awesome App',
    'description' => 'A productivity app that helps you organize your tasks',
    'applicationCategory' => 'ProductivityApplication',
    'operatingSystem' => ['Android', 'iOS'],
    'offers' => [
        'price' => '0',
        'priceCurrency' => 'USD',
        'availability' => 'https://schema.org/InStock'
    ],
    'aggregateRating' => [
        'ratingValue' => 4.5,
        'ratingCount' => 1250
    ],
    'screenshot' => [
        '/images/screenshot1.png',
        '/images/screenshot2.png'
    ],
    'image' => '/images/app-icon.png',
    'url' => 'https://myapp.com',
    'softwareVersion' => '2.1.0',
    'datePublished' => '2023-01-01'
]);
```

### Book Schema

Ideal for bookstores, libraries, and publishing websites:

```php
SEO::addBook([
    'name' => 'The Art of Laravel',
    'description' => 'A comprehensive guide to Laravel framework',
    'author' => [
        ['name' => 'John Doe'],
        ['name' => 'Jane Smith']
    ],
    'isbn' => '978-0-123456-78-9',
    'datePublished' => '2024-01-01',
    'publisher' => [
        'name' => 'Tech Books Publishing',
        'url' => 'https://techbooks.com'
    ],
    'bookFormat' => 'Hardcover',
    'numberOfPages' => 350,
    'image' => '/images/book-cover.jpg',
    'url' => 'https://example.com/book',
    'inLanguage' => 'en',
    'genre' => ['Technology', 'Programming']
]);
```

### Movie Schema

Perfect for movie databases, streaming platforms, and entertainment sites:

```php
SEO::addMovie([
    'name' => 'The Great Adventure',
    'description' => 'An epic journey through time and space',
    'image' => '/images/movie-poster.jpg',
    'datePublished' => '2024-01-15',
    'director' => [
        ['name' => 'Director Name']
    ],
    'actor' => [
        ['name' => 'Actor One'],
        ['name' => 'Actor Two']
    ],
    'genre' => ['Action', 'Adventure', 'Sci-Fi'],
    'duration' => 'PT2H30M',
    'aggregateRating' => [
        'ratingValue' => 8.5,
        'ratingCount' => 5000,
        'bestRating' => 10.0
    ],
    'contentRating' => 'PG-13',
    'productionCompany' => [
        ['name' => 'Production Company']
    ],
    'countryOfOrigin' => ['US'],
    'inLanguage' => ['en', 'ar']
]);
```

### Podcast Schema

Great for podcast platforms and audio content websites:

```php
SEO::addPodcast([
    'name' => 'Tech Talk Podcast',
    'description' => 'Weekly discussions about technology and innovation',
    'image' => '/images/podcast-cover.jpg',
    'author' => [
        'name' => 'Podcast Host',
        'email' => 'host@example.com'
    ],
    'publisher' => [
        'name' => 'Podcast Network',
        'url' => 'https://podcast-network.com'
    ],
    'url' => 'https://example.com/podcast',
    'inLanguage' => 'ar',
    'category' => ['Technology', 'Business'],
    'episode' => [
        'name' => 'Episode 1: Getting Started',
        'description' => 'In this episode, we discuss...',
        'datePublished' => '2024-01-15',
        'duration' => 'PT30M',
        'episodeNumber' => 1
    ],
    'aggregateRating' => [
        'ratingValue' => 4.6,
        'ratingCount' => 120
    ]
]);
```

### Enhanced Video Schema

Improved VideoObject with additional features:

```php
SEO::addVideo([
    'name' => 'Tutorial Video',
    'description' => 'Learn how to use Laravel SEO package',
    'video_url' => 'https://youtube.com/watch?v=...',
    'image' => '/images/video-thumbnail.jpg',
    'duration' => 'PT15M30S',
    'contentUrl' => 'https://example.com/video.mp4',
    'uploadDate' => '2024-01-15',
    'interactionStatistic' => [
        [
            '@type' => 'InteractionCounter',
            'interactionType' => 'https://schema.org/WatchAction',
            'userInteractionCount' => 10000
        ],
        [
            '@type' => 'InteractionCounter',
            'interactionType' => 'https://schema.org/LikeAction',
            'userInteractionCount' => 500
        ]
    ]
]);
```

## Geo-targeting

Add geographic targeting meta tags for location-based SEO:

```php
// In config/seo.php
'geo_targeting' => [
    'enabled' => true,
    'country' => 'SY',
    'region' => 'SY-DI', // Damascus
    'placename' => 'Damascus',
    'latitude' => 33.5138,
    'longitude' => 36.2765,
],
```

This automatically generates:
- `geo.region` meta tag
- `geo.placename` meta tag
- `geo.position` meta tag
- `ICBM` meta tag

## Social Media Optimization

### Pinterest Rich Pins

```php
// In config/seo.php
'social' => [
    'pinterest' => [
        'verify' => 'your-pinterest-verification-code',
    ],
],
```

### WhatsApp & Telegram

Both WhatsApp and Telegram use OpenGraph tags, so they work automatically. The package optimizes images for better previews.

## Performance Optimization (Enhanced)

### Prefetch, Prerender, and Modulepreload

```php
// In config/seo.php
'performance' => [
    'prefetch' => [
        '/next-page',
        '/related-article',
    ],
    'prerender' => [
        '/important-page',
    ],
    'modulepreload' => [
        [
            'href' => '/js/app.js',
            'type' => 'module',
        ],
    ],
    'preload' => [
        [
            'href' => '/css/critical.css',
            'as' => 'style',
            'onload' => "this.onload=null;this.rel='stylesheet'", // Critical CSS
        ],
    ],
],
```

## Commands

### Test Schema

Test your JSON-LD schemas:

```bash
php artisan seo:test-schema
php artisan seo:test-schema https://example.com
php artisan seo:test-schema --format=table
```

This command:
- Fetches the page HTML
- Extracts all JSON-LD schemas
- Validates JSON structure
- Displays schema types and status

### Health Check

Check your SEO configuration:

```bash
php artisan seo:health-check
```

This command checks:
- ✅ Site configuration (name, description, URL)
- ✅ Social media settings (Twitter, Facebook)
- ✅ Analytics setup (GA4, GTM)
- ✅ Multilingual configuration
- ✅ Organization schema
- ✅ Provides a health score (0-100%)

## Complete Guide

For complete examples of Controller and Model integration for all page types, see:

- **[COMPLETE_GUIDE.md](COMPLETE_GUIDE.md)** - Comprehensive guide with explanations
- **[EXAMPLES_TEST.md](EXAMPLES_TEST.md)** - Ready-to-use code examples you can copy and paste

These guides include:
- ✅ Full Model examples for all page types
- ✅ Full Controller examples
- ✅ Migration examples
- ✅ Route examples
- ✅ View examples
- ✅ What gets generated automatically
- ✅ Advanced examples
- ✅ View integration
- ✅ Best practices
- ✅ Troubleshooting
- ✅ Testing checklist

## Model Integration

The package provides a professional `HasSEO` trait that automatically detects and extracts SEO data from your models.

### Quick Setup

#### Step 1: Add Trait to Model

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Shammaa\LaravelSEO\Traits\HasSEO;

class Post extends Model
{
    use HasSEO;
    
    // Define relationships
    public function writer()
    {
        return $this->belongsTo(Writer::class);
    }
    
    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }
    
    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
    
    public function faqs()
    {
        return $this->hasMany(FAQ::class)->orderBy('order');
    }
    
    public function steps()
    {
        return $this->hasMany(TutorialStep::class)->orderBy('order');
    }
    
    public function review()
    {
        return $this->hasOne(Review::class);
    }
    
    public function event()
    {
        return $this->hasOne(Event::class);
    }
}
```

#### Step 2: Use in Controller

```php
public function show(Post $post)
{
    // That's it! The library does everything automatically
    SEO::post($post)->set();
    
    return view('posts.show', compact('post'));
}
```

**The library automatically:**
- ✅ Loads required relationships (eager loading)
- ✅ Detects FAQs from relationship or JSON column
- ✅ Detects HowTo steps from relationship or JSON column
- ✅ Detects Review data from relationship or attributes
- ✅ Detects Event data from relationship or attributes
- ✅ Extracts all SEO data (title, description, image, etc.)

### Customizing Field Mappings

Each model can define its own field names:

```php
class Post extends Model
{
    use HasSEO;

    /**
     * Define field names in your Model
     */
    protected function getSEOFieldMap(): array
    {
        return [
            'title' => ['title', 'name', 'headline'], // Try these fields in order
            'description' => ['content', 'text', 'description', 'excerpt'],
            'image' => ['photo', 'image', 'thumbnail', 'cover'],
            'published_at' => ['created_at', 'published_at', 'publish_date'],
            'modified_at' => ['updated_at', 'modified_at', 'last_updated'],
        ];
    }

    /**
     * Define relationship names in your Model
     */
    protected function getSEORelationships(): array
    {
        return [
            'writer' => 'author',        // If relationship name is 'author' instead of 'writer'
            'categories' => 'categories',
            'tags' => 'tags',
            'faqs' => 'faqs',
            'steps' => 'tutorial_steps',  // If relationship name is different
            'review' => 'product_review',
            'event' => 'event_data',
        ];
    }
}
```

### Example: Different Model Structure

```php
class Article extends Model
{
    use HasSEO;

    protected function getSEOFieldMap(): array
    {
        return [
            'title' => ['headline', 'article_title'], // Different fields
            'description' => ['summary', 'article_summary'],
            'image' => ['cover_image', 'featured_image'],
            'published_at' => ['publish_date', 'published_on'],
            'modified_at' => ['last_modified', 'updated_on'],
        ];
    }

    protected function getSEORelationships(): array
    {
        return [
            'writer' => 'author',           // Different relationship name
            'categories' => 'sections',     // Different relationship name
            'tags' => 'keywords',          // Different relationship name
            'faqs' => 'questions',         // Different relationship name
            'steps' => 'instructions',     // Different relationship name
            'review' => 'rating',          // Different relationship name
            'event' => 'event_info',       // Different relationship name
        ];
    }
}
```

### Customizing Relationship Loading

You can customize which relationships are loaded:

```php
class Post extends Model
{
    use HasSEO;

    /**
     * (Optional) Customize relationships to be loaded automatically
     */
    public function getSEORelationshipsToLoad(): array
    {
        $relationships = parent::getSEORelationshipsToLoad();
        
        // Example: If Post is tutorial type, ensure steps are loaded
        if ($this->type === 'tutorial') {
            $relationships[] = 'steps';
        }
        
        // Example: If Post is review type, ensure review is loaded
        if ($this->type === 'review') {
            $relationships[] = 'review';
        }
        
        return array_unique($relationships);
    }
}
```

### Overriding Methods

You can override methods for custom logic:

```php
class Post extends Model
{
    use HasSEO;

    // Customize FAQ extraction
    public function getSEOFAQs(): array
    {
        return $this->faqs()
            ->where('is_active', true)
            ->orderBy('order')
            ->get()
            ->map(function($faq) {
                return [
                    'question' => $faq->question,
                    'answer' => $faq->answer,
                ];
            })
            ->toArray();
    }

    // Customize HowTo Steps extraction
    public function getSEOHowToSteps(): array
    {
        return $this->steps()
            ->where('published', true)
            ->orderBy('order')
            ->get()
            ->map(function($step) {
                return [
                    'name' => $step->title,
                    'text' => $step->content,
                    'image' => $step->getImageUrl(), // Custom method
                ];
            })
            ->toArray();
    }

    // Customize Review extraction
    public function getSEOReview(): ?array
    {
        if (!$this->review) {
            return null;
        }

        return [
            'itemName' => $this->review->product->name,
            'ratingValue' => $this->review->rating,
            'bestRating' => 5.0,
            'reviewBody' => $this->review->content,
            'authorName' => $this->writer->name,
            'datePublished' => $this->created_at->toIso8601String(),
        ];
    }
}
```

### Model Requirements

The package automatically detects model attributes. Here's what it looks for:

#### Post Model
- **Title**: `title`, `name`
- **Description**: `content`, `text`, `description`
- **Image**: `photo`, `image`, `thumbnail`
- **Dates**: `created_at`, `published_at`, `updated_at`, `modified_at`
- **Relationships**: `writer`, `categories`, `tags`, `faqs`, `steps`, `review`, `event`
- **Optional**: `slug`, `video_url`

#### Category Model
- **Name**: `name`, `title`
- **Description**: `description` (optional)
- **Image**: `photo`, `image`, `thumbnail` (optional)
- **Relationships**: `parent` (optional, for breadcrumbs)
- **Method**: `route()` (optional, for breadcrumb URLs)

#### Product Model (E-commerce)
- **Name**: `name`, `title`, `product_name`
- **Description**: `description`, `product_description`
- **Image**: `image`, `photo`, `product_image`
- **Price**: `price`, `sale_price`, `current_price`
- **Currency**: `currency`, `price_currency` (default: USD)
- **Availability**: `availability`, `in_stock`, `stock_quantity`
- **SKU**: `sku` (optional)
- **MPN**: `mpn` (optional)
- **GTIN**: `gtin` (optional)
- **Condition**: `condition` (optional)
- **Relationships**: `brand`, `category`, `reviews`
- **Properties**: `color`, `size`, `material`, `weight`, `height`, `width`, `depth` (optional)

#### Tag Model
- **Name**: `name`, `title`
- **Method**: `route()` (optional, for breadcrumb URLs)

#### Author Model
- **Name**: `name`, `username`, `title`
- **Method**: `route()` (optional, for breadcrumb URLs)

#### Archive (String or Object)
- **String**: Date string like `"2024-01"` or `"January 2024"`
- **Object**: Model with `name`, `title`, or `date` attribute

#### Page Model (Static Pages)
- **Name**: `title`, `name`
- **Relationships**: `parent` (optional, for breadcrumbs)
- **Method**: `route()` (optional, for breadcrumb URLs)

## Advanced Features

### Multilingual & Hreflang Support

Enable multilingual SEO with hreflang tags:

```php
'multilingual' => [
    'enabled' => true,
    'locales' => ['ar', 'en', 'fr'],
    'default_locale' => 'ar',
    'x_default' => true,
    'url_generator' => function($locale, $model, $currentUrl) {
        // Custom URL generation logic
        return str_replace('/ar/', "/{$locale}/", $currentUrl);
    },
],
```

**Custom URL Generator Example:**
```php
'url_generator' => function($locale, $model, $currentUrl) {
    if ($model && method_exists($model, 'getLocalizedUrl')) {
        return $model->getLocalizedUrl($locale);
    }
    return str_replace('/en/', "/{$locale}/", $currentUrl);
},
```

### Reading Time

Reading time is automatically calculated and displayed:

```php
'reading_time' => [
    'enabled' => true,
    'words_per_minute' => 200, // Average reading speed
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
        // Add your custom translations
        'custom_locale' => ':minutes custom text',
    ],
],
```

**Translation Format:**
- Use `:minutes` placeholder for the number of minutes
- The library automatically replaces `:minutes` with the calculated value
- If a translation for the current locale is not found, it falls back to English

**Customization:**
```php
'reading_time' => [
    'translations' => [
        'en' => ':minutes minutes',
        'ar' => ':minutes دقيقة',
        'custom' => 'Takes :minutes minutes to read',
    ],
],
```

Reading time is automatically:
- Added to Twitter Cards as `twitter:label1` and `twitter:data1`
- Added to NewsArticle Schema as `timeRequired` (ISO 8601 format: PT5M)

**Using ReadingTimeCalculator directly:**
```php
use Shammaa\LaravelSEO\Helpers\ReadingTimeCalculator;

$minutes = ReadingTimeCalculator::calculate($content, 200);
// Returns: 5 (minutes)

$iso8601 = ReadingTimeCalculator::toIso8601($content);
// Returns: "PT5M"

$formatted = ReadingTimeCalculator::format(
    $content, 
    200, 
    'ar', 
    config('seo.reading_time.translations')
);
// Returns: "5 دقيقة قراءة" (or custom translation from config)
```

### AMP (Accelerated Mobile Pages) Support

```php
'amp' => [
    'enabled' => true,
    'url_generator' => function($model) {
        return route('amp.post', $model->slug);
    },
],
```

When enabled, automatically adds `<link rel="amphtml">` tag for post pages.

### RSS/Atom Feeds

```php
'rss' => [
    'enabled' => true,
    'url' => '/feed',
],
```

Adds `<link rel="alternate" type="application/rss+xml">` tag.

### Pagination Support

```php
'pagination' => [
    'enabled' => true,
],
```

Automatically adds `<link rel="prev">` and `<link rel="next">` tags if your model has `previous` and `next` relationships with `route()` methods.

**Model Example:**
```php
class Post extends Model
{
    public function previous()
    {
        return static::where('id', '<', $this->id)
            ->orderBy('id', 'desc')
            ->first();
    }
    
    public function next()
    {
        return static::where('id', '>', $this->id)
            ->orderBy('id', 'asc')
            ->first();
    }
}
```

### Performance Optimization

Improve page load speed with resource hints:

```php
'performance' => [
    'dns_prefetch' => [
        'cdn.example.com',
        'fonts.googleapis.com',
    ],
    'preconnect' => [
        'https://fonts.googleapis.com',
        'https://fonts.gstatic.com',
    ],
    'preload' => [
        [
            'href' => '/fonts/main.woff2',
            'as' => 'font',
            'type' => 'font/woff2',
        ],
        [
            'href' => '/images/hero.jpg',
            'as' => 'image',
        ],
    ],
],
```

### Mobile Optimization

```php
'mobile' => [
    'theme_color' => '#ffffff',
    'apple_mobile_web_app' => [
        'enabled' => true,
        'status_bar_style' => 'default', // default, black, black-translucent
        'title' => 'My App',
    ],
    'manifest' => '/manifest.json',
],
```

### Security Headers

```php
'security' => [
    'content_security_policy' => "default-src 'self'",
    'referrer_policy' => 'strict-origin-when-cross-origin',
    'x_frame_options' => 'SAMEORIGIN', // DENY, SAMEORIGIN, ALLOW-FROM
    'x_content_type_options' => 'nosniff',
],
```

### Analytics Integration

Support for multiple analytics platforms:

```php
'analytics' => [
    'ga4' => [
        'measurement_id' => 'G-XXXXXXXXXX', // Google Analytics 4
    ],
    'gtm' => [
        'container_id' => 'GTM-XXXXXXX', // Google Tag Manager
    ],
    'yandex' => [
        'counter_id' => '12345678', // Yandex Metrica
    ],
    'facebook' => [
        'pixel_id' => '123456789012345', // Facebook Pixel
    ],
],
```

All analytics scripts are automatically injected when configured.

### Image Rendering Configuration

Configure image loading behavior:

```php
'image_rendering' => [
    'loading' => 'lazy', // lazy, eager
    'decoding' => 'async', // async, sync, auto
    'fetchpriority' => null, // high, low, auto (for important images)
],
```

## Configuration Reference

### Site Information

```php
'site' => [
    'name' => 'Your Site Name',
    'description' => 'Your site description',
    'url' => 'https://yoursite.com',
    'logo' => 'path/to/logo.jpg',
    'publisher' => 'Publisher Name',
],
```

### Image Route

If you're using an image processing package (like `laravel-smart-glide`):

```php
'image_route' => [
    'name' => 'image', // Route name
    'og_size' => '1200x630',
    'twitter_size' => '1200x630',
    'linkedin_size' => '1200x627',
    'logo_size' => '265x85',
],
```

### Social Media

```php
'social' => [
    'twitter' => [
        'card_type' => 'summary_large_image',
        'site' => '@yourhandle',
        'creator' => '@yourhandle',
    ],
    'facebook' => [
        'app_id' => 'your-app-id',
    ],
],
```

### Organization Schema

```php
'organization' => [
    'name' => 'Your Organization',
    'alternate_name' => 'Alternate Name',
    'description' => 'Organization description',
    'same_as' => [
        'https://www.facebook.com/yourpage',
        'https://twitter.com/yourhandle',
    ],
    'contact_point' => [
        'email' => 'info@example.com',
        'contact_type' => 'customer service',
    ],
],
```

### Fallback Texts

Customize default texts:

```php
'defaults' => [
    'fallbacks' => [
        'post_title' => 'Post',
        'category_name' => 'Category',
        'category_description' => 'Latest news in :name category',
        'search_title' => 'Search results for: :query - :site',
        'search_description' => 'Find news and articles about: :query',
    ],
],
```

## Environment Variables

You can configure SEO via environment variables:

### Basic Settings
```env
SEO_SITE_NAME="Your Site Name"
SEO_SITE_DESCRIPTION="Your site description"
SEO_SITE_URL="https://yoursite.com"
SEO_SITE_LOGO="path/to/logo.jpg"
SEO_SITE_PUBLISHER="Publisher Name"
SEO_CACHE_TTL=86400
```

### Social Media
```env
SEO_TWITTER_SITE="@yourhandle"
SEO_TWITTER_CREATOR="@yourhandle"
SEO_TWITTER_CARD_TYPE="summary_large_image"
SEO_FACEBOOK_APP_ID="your-app-id"
```

### Multilingual
```env
SEO_MULTILINGUAL_ENABLED=true
SEO_MULTILINGUAL_LOCALES=["ar","en"]
SEO_MULTILINGUAL_DEFAULT="ar"
SEO_MULTILINGUAL_X_DEFAULT=true
SEO_BREADCRUMB_HOME_LABEL="Home"
```

### Reading Time
```env
SEO_READING_TIME_ENABLED=true
SEO_READING_TIME_WPM=200
```

### AMP
```env
SEO_AMP_ENABLED=true
```

### RSS
```env
SEO_RSS_ENABLED=true
SEO_RSS_URL="/feed"
```

### Performance
```env
SEO_IMAGE_LOADING="lazy"
SEO_IMAGE_DECODING="async"
```

### Mobile
```env
SEO_MOBILE_THEME_COLOR="#ffffff"
SEO_APPLE_MOBILE_WEB_APP=true
SEO_APPLE_STATUS_BAR_STYLE="default"
SEO_MOBILE_MANIFEST="/manifest.json"
```

### Security
```env
SEO_CSP="default-src 'self'"
SEO_REFERRER_POLICY="strict-origin-when-cross-origin"
SEO_X_FRAME_OPTIONS="SAMEORIGIN"
SEO_X_CONTENT_TYPE_OPTIONS="nosniff"
```

### Analytics
```env
SEO_GA4_MEASUREMENT_ID="G-XXXXXXXXXX"
SEO_GTM_CONTAINER_ID="GTM-XXXXXXX"
SEO_YANDEX_COUNTER_ID="12345678"
SEO_FACEBOOK_PIXEL_ID="123456789012345"
```

### Organization
```env
SEO_ORG_NAME="Your Organization"
SEO_ORG_ALTERNATE_NAME="Alternate Name"
SEO_ORG_DESCRIPTION="Organization description"
SEO_ORG_EMAIL="info@example.com"
SEO_ORG_ADDRESS_COUNTRY="SY"
SEO_ORG_ADDRESS_LOCALITY="Damascus"
SEO_ORG_FOUNDING_DATE="2011"
```

## API Reference

### Facade Methods

#### `SEO::for(string $pageType, $model = null)`
Set page type and model.

#### `SEO::home()`
Set page type to home.

#### `SEO::post($model)`
Set page type to post with model.

#### `SEO::category($model)`
Set page type to category with model.

#### `SEO::search(array $params)`
Set page type to search with query parameters.

#### `SEO::set()`
Generate and set all SEO tags. Must be called after setting page type.

#### `SEO::render()`
Render all SEO tags as HTML string. Automatically calls `set()` if not called.

#### `SEO::breadcrumb()`
Get breadcrumb items as array.

#### `SEO::addFAQ(array $faqs)`
Add FAQ schema. Returns self for chaining.

**Parameters:**
- `$faqs` - Array of FAQ items, each with `question` and `answer` keys

#### `SEO::addHowTo(string $name, array $steps, ?string $description = null, ?string $image = null)`
Add HowTo schema. Returns self for chaining.

**Parameters:**
- `$name` - Name of the HowTo
- `$steps` - Array of steps (strings or arrays with `name`, `text`, `image`, `url`)
- `$description` - Optional description
- `$image` - Optional image URL

#### `SEO::addReview(string $itemName, float $ratingValue, float $bestRating = 5.0, ?string $reviewBody = null, ?string $authorName = null, ?string $datePublished = null)`
Add Review schema. Returns self for chaining.

**Parameters:**
- `$itemName` - Name of the item being reviewed
- `$ratingValue` - Rating value (e.g., 4.5)
- `$bestRating` - Best possible rating (default: 5.0)
- `$reviewBody` - Optional review text
- `$authorName` - Optional reviewer name
- `$datePublished` - Optional publication date (ISO 8601 format)

#### `SEO::addEvent(string $name, string $startDate, ?string $endDate = null, ?string $description = null, ?string $locationName = null, ?string $locationAddress = null, ?string $image = null, ?string $organizerName = null, ?string $organizerUrl = null)`
Add Event schema. Returns self for chaining.

**Parameters:**
- `$name` - Event name
- `$startDate` - Start date (ISO 8601 format)
- `$endDate` - Optional end date (ISO 8601 format)
- `$description` - Optional description
- `$locationName` - Optional location name
- `$locationAddress` - Optional location address
- `$image` - Optional image URL
- `$organizerName` - Optional organizer name
- `$organizerUrl` - Optional organizer URL

#### `SEO::product($model)`
Set page type to product with model.

#### `SEO::addProduct($model)`
Add Product schema. Returns self for chaining. Automatically called when using `SEO::product()->set()`.

**Parameters:**
- `$model` - Product model with price, brand, category, etc.

#### `SEO::addAggregateRating(float $ratingValue, int $ratingCount, float $bestRating = 5.0, float $worstRating = 1.0)`
Add AggregateRating schema. Returns self for chaining.

**Parameters:**
- `$ratingValue` - Average rating value (e.g., 4.5)
- `$ratingCount` - Number of ratings/reviews
- `$bestRating` - Best possible rating (default: 5.0)
- `$worstRating` - Worst possible rating (default: 1.0)

#### `SEO::addBrand(string $name, ?string $logo = null, ?string $url = null)`
Add Brand schema. Returns self for chaining.

**Parameters:**
- `$name` - Brand name
- `$logo` - Optional brand logo URL
- `$url` - Optional brand website URL

### HasSEO Trait Methods

#### `getSEOFieldMap(): array`
Define field mappings for your model. Override in your model.

#### `getSEORelationships(): array`
Define relationship names for your model. Override in your model.

#### `getSEORelationshipsToLoad(): array`
Define which relationships to eager load. Override in your model.

#### `getSEOTitle(): ?string`
Get SEO title from model.

#### `getSEODescription(): ?string`
Get SEO description from model.

#### `getSEOImage(): ?string`
Get SEO image from model.

#### `getSEOKeywords(): array`
Get SEO keywords from model (from tags and categories).

#### `getSEOAuthor(): ?string`
Get SEO author name from model.

#### `getSEOPublishedAt(): ?string`
Get published date in ISO 8601 format.

#### `getSEOModifiedAt(): ?string`
Get modified date in ISO 8601 format.

#### `getSEOFAQs(): array`
Get FAQs for FAQ schema. Override for custom logic.

#### `getSEOHowToSteps(): array`
Get HowTo steps. Override for custom logic.

#### `getSEOReview(): ?array`
Get Review data. Override for custom logic.

#### `getSEOEvent(): ?array`
Get Event data. Override for custom logic.

#### `hasSEOFAQs(): bool`
Check if model has FAQs.

#### `hasSEOHowToSteps(): bool`
Check if model has HowTo steps.

#### `hasSEOReview(): bool`
Check if model has Review data.

#### `hasSEOEvent(): bool`
Check if model has Event data.

## Examples

### Complete Example: Post with All Features

```php
// Controller
use Shammaa\LaravelSEO\Facades\SEO;

public function show(Post $post)
{
    // Basic SEO setup
    SEO::post($post)->set();
    
    // Add FAQ if article has questions
    if ($post->has_faq) {
        SEO::addFAQ([
            ['question' => 'What is this?', 'answer' => 'This is...'],
            ['question' => 'How does it work?', 'answer' => 'It works by...'],
        ]);
    }
    
    // Add HowTo if it's a tutorial
    if ($post->is_tutorial) {
        SEO::addHowTo(
            name: $post->title,
            steps: $post->steps->pluck('content')->toArray(),
            description: $post->description,
            image: $post->image
        );
    }
    
    // Add Review if it's a review article
    if ($post->is_review && $post->rating) {
        SEO::addReview(
            itemName: $post->reviewed_item,
            ratingValue: $post->rating,
            bestRating: 5.0,
            reviewBody: $post->review_content,
            authorName: $post->writer->name ?? null
        );
    }
    
    return view('posts.show', compact('post'));
}
```

```blade
{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    {{-- SEO Meta Tags --}}
    {!! SEO::render() !!}
    
    {{-- Custom Schemas (JSON-LD) --}}
    {!! $customSchemas ?? '' !!}
</head>
<body>
    {{-- Breadcrumb Navigation --}}
    @if(isset($breadcrumbs))
        @include('seo::breadcrumb')
    @endif
    
    <main>
        @yield('content')
    </main>
</body>
</html>
```

### Example: Using HasSEO Trait

```php
// Model
class Post extends Model
{
    use HasSEO;
    
    protected function getSEOFieldMap(): array
    {
        return [
            'title' => ['title', 'name'],
            'description' => ['content', 'text'],
            'image' => ['photo', 'image'],
            'published_at' => ['created_at', 'published_at'],
            'modified_at' => ['updated_at', 'modified_at'],
        ];
    }
    
    protected function getSEORelationships(): array
    {
        return [
            'writer' => 'writer',
            'categories' => 'categories',
            'tags' => 'tags',
            'faqs' => 'faqs',
            'steps' => 'steps',
            'review' => 'review',
            'event' => 'event',
        ];
    }
    
    public function faqs()
    {
        return $this->hasMany(FAQ::class)->orderBy('order');
    }
    
    public function steps()
    {
        return $this->hasMany(TutorialStep::class)->orderBy('order');
    }
    
    public function review()
    {
        return $this->hasOne(Review::class);
    }
    
    public function event()
    {
        return $this->hasOne(Event::class);
    }
}

// Controller - That's it!
public function show(Post $post)
{
    SEO::post($post)->set(); // Automatically detects and loads everything!
    
    return view('posts.show', compact('post'));
}
```

### Example: Extracting Data from Database

```php
// Extract FAQs from relationship
if ($post->faqs && $post->faqs->isNotEmpty()) {
    $faqs = $post->faqs->map(function($faq) {
        return [
            'question' => $faq->question,
            'answer' => $faq->answer,
        ];
    })->toArray();
    
    SEO::addFAQ($faqs);
}

// Extract HowTo steps from relationship
if ($post->steps && $post->steps->isNotEmpty()) {
    $steps = $post->steps->map(function($step) {
        return [
            'name' => $step->title,
            'text' => $step->content,
            'image' => $step->image,
        ];
    })->toArray();
    
    SEO::addHowTo($post->title, $steps);
}

// Extract Review from model
if ($post->review) {
    SEO::addReview(
        itemName: $post->review->product_name,
        ratingValue: $post->review->rating,
        bestRating: 5.0,
        reviewBody: $post->review->content,
        authorName: $post->review->author->name ?? null,
        datePublished: $post->review->created_at->toIso8601String()
    );
}

// Extract Event from model
if ($post->event) {
    SEO::addEvent(
        name: $post->title,
        startDate: $post->event->start_date->toIso8601String(),
        endDate: $post->event->end_date?->toIso8601String(),
        description: $post->description,
        locationName: $post->event->location_name,
        locationAddress: $post->event->location_address,
        image: $post->image,
        organizerName: $post->event->organizer->name ?? null,
        organizerUrl: $post->event->organizer->url ?? null
    );
}
```

## Differences: Blog vs E-commerce

### Blog/Article Website

**Focus:** Content, articles, news
**Schemas:** NewsArticle, WebPage, BreadcrumbList
**Key Features:**
- Article metadata (author, publisher, dates)
- Reading time
- Categories and tags
- FAQ, HowTo, Review, Event schemas

**Example:**
```php
SEO::post($article)->set();
```

### E-commerce Website

**Focus:** Products, sales, shopping
**Schemas:** Product, Offer, AggregateRating, Brand
**Key Features:**
- Product metadata (SKU, MPN, GTIN)
- Price and offers
- Availability status
- Aggregate ratings from reviews
- Brand information
- Product properties (color, size, material)

**Example:**
```php
SEO::product($product)->set();
```

### Hybrid Website (Both)

You can use both on the same website:

```php
// For articles
SEO::post($article)->set();

// For products
SEO::product($product)->set();
```

## Troubleshooting

### Tags Not Appearing

1. **Check if `set()` is called:**
   ```php
   SEO::post($post)->set(); // Must call set() first
   ```

2. **Check if `render()` is called in view:**
   ```blade
   {!! SEO::render() !!}
   ```

3. **Check config file:**
   ```bash
   php artisan config:clear
   ```

### Model Data Not Detected

1. **Check field mappings:**
   ```php
   protected function getSEOFieldMap(): array
   {
       return [
           'title' => ['your_field_name'], // Add your field names
       ];
   }
   ```

2. **Check relationships:**
   ```php
   protected function getSEORelationships(): array
   {
       return [
           'writer' => 'your_relationship_name', // Add your relationship names
       ];
   }
   ```

### Breadcrumb Not Showing

1. **Check if breadcrumb is shared:**
   ```blade
   @if(isset($breadcrumbs))
       @include('seo::breadcrumb')
   @endif
   ```

2. **Check model relationships:**
   - Post should have `categories` relationship
   - Category should have `parent` relationship and `route()` method

### Reading Time Not Showing

1. **Check if enabled:**
   ```php
   'reading_time' => [
       'enabled' => true,
   ],
   ```

2. **Check if model has content:**
   ```php
   // Model should have 'content' attribute
   $post->content; // Should exist
   ```

### Schemas Not Auto-Detected

1. **Check if HasSEO trait is used:**
   ```php
   use Shammaa\LaravelSEO\Traits\HasSEO;
   ```

2. **Check relationships:**
   ```php
   public function faqs() {
       return $this->hasMany(FAQ::class);
   }
   ```

3. **Check if relationships are loaded:**
   ```php
   // The library automatically loads relationships
   // But you can manually load if needed:
   $post->load('faqs', 'steps', 'review', 'event');
   ```

## Best Practices

### 1. Use HasSEO Trait

Always use the `HasSEO` trait for automatic data extraction:

```php
class Post extends Model
{
    use HasSEO;
}
```

### 2. Define Field Mappings

Always define field mappings in your models:

```php
protected function getSEOFieldMap(): array
{
    return [
        'title' => ['title', 'name'],
        // ... your fields
    ];
}
```

### 3. Use Eager Loading

The library automatically eager loads relationships, but you can optimize:

```php
// In Controller
$post->load(['writer', 'categories', 'tags', 'faqs', 'steps']);
```

### 4. Cache Site Data

Site data is cached by default (24 hours). Adjust if needed:

```php
'cache_ttl' => 86400, // 24 hours in seconds
```

### 5. Use Environment Variables

Store sensitive data in `.env`:

```env
SEO_SITE_NAME="Your Site"
SEO_TWITTER_SITE="@yourhandle"
```

### 6. Test Your Schemas

Use Google's Rich Results Test:
- https://search.google.com/test/rich-results

### 7. Validate JSON-LD

Use JSON-LD Playground:
- https://json-ld.org/playground/

### 8. Monitor Performance

Use performance optimization features:

```php
'performance' => [
    'dns_prefetch' => ['cdn.example.com'],
    'preconnect' => ['https://fonts.googleapis.com'],
],
```

## How It Works

### Automatic Detection

The package automatically detects model attributes:

1. **Title**: Checks `title`, `name` attributes
2. **Description**: Checks `content`, `text`, `description` attributes
3. **Image**: Checks `photo`, `image`, `thumbnail` attributes
4. **Dates**: Checks `created_at`, `published_at`, `updated_at`, `modified_at`
5. **Author**: Checks `writer` relationship or uses site name
6. **Categories**: Checks `categories` relationship for breadcrumbs
7. **Tags**: Checks `tags` relationship for article:tag meta tags
8. **Video**: Checks `video_url` for VideoObject schema

### Schema Generation Flow

1. **Page Type Detection**: Determines page type (home, post, category, search)
2. **Data Extraction**: Extracts data from model or config
3. **Meta Tags**: Generates standard meta tags
4. **OpenGraph**: Generates Facebook OpenGraph tags
5. **Twitter Cards**: Generates Twitter Card tags with reading time
6. **Schemas**: Generates appropriate JSON-LD schemas
7. **Additional Features**: Adds multilingual, performance, analytics tags

### Caching

Site data is cached for 24 hours by default (configurable via `SEO_CACHE_TTL`). This improves performance by avoiding repeated database queries.

### View Sharing

The package automatically shares data with views:
- `$customSchemas` - All JSON-LD schemas
- `$breadcrumbs` - Breadcrumb items (for post/category pages)
- `$performanceTags` - Performance optimization tags
- `$analyticsTags` - Analytics scripts
- `$ampUrl` - AMP URL (if enabled)
- `$paginationLinks` - Prev/Next links (if available)

## Requirements

- PHP 8.2+
- Laravel 10.0+ or 11.0+

## License

MIT

## Author

Shadi Shammaa

---

## Support

For issues, questions, or contributions, please visit the [GitHub repository](https://github.com/shammaa/laravel-seo).
