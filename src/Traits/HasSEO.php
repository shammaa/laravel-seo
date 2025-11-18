<?php

declare(strict_types=1);

namespace Shammaa\LaravelSEO\Traits;

use Carbon\Carbon;

trait HasSEO
{
    /**
     * Get SEO field mappings for this model
     * Override in your model to define custom field names
     * 
     * @return array
     */
    protected function getSEOFieldMap(): array
    {
        return [
            'title' => ['title', 'name'],
            'description' => ['content', 'text', 'description'],
            'image' => ['photo', 'image', 'thumbnail'],
            'published_at' => ['created_at', 'published_at'],
            'modified_at' => ['updated_at', 'modified_at'],
        ];
    }

    /**
     * Get SEO relationship names for this model
     * Override in your model to define custom relationship names
     * 
     * @return array
     */
    protected function getSEORelationships(): array
    {
        return [
            'writer' => 'writer',
            'author' => 'author',
            'categories' => 'categories',
            'tags' => 'tags',
            'faqs' => 'faqs',
            'steps' => 'steps',
            'review' => 'review',
            'event' => 'event',
        ];
    }

    /**
     * Get relationships that should be eager loaded for SEO
     * Override in your model to customize which relationships to load
     * 
     * @return array
     */
    public function getSEORelationshipsToLoad(): array
    {
        $relationships = $this->getSEORelationships();
        $toLoad = [];

        // Always load basic relationships
        if (!empty($relationships['writer'])) {
            $toLoad[] = $relationships['writer'];
        }
        if (!empty($relationships['author']) && $relationships['author'] !== $relationships['writer']) {
            $toLoad[] = $relationships['author'];
        }
        if (!empty($relationships['categories'])) {
            $toLoad[] = $relationships['categories'];
        }
        if (!empty($relationships['tags'])) {
            $toLoad[] = $relationships['tags'];
        }

        // Conditionally load based on model type or data
        $seoType = $this->getSEOType();
        
        if ($seoType === 'tutorial' && !empty($relationships['steps'])) {
            $toLoad[] = $relationships['steps'];
        }
        
        if (!empty($relationships['faqs'])) {
            $toLoad[] = $relationships['faqs'];
        }
        
        if (!empty($relationships['review'])) {
            $toLoad[] = $relationships['review'];
        }
        
        if (!empty($relationships['event'])) {
            $toLoad[] = $relationships['event'];
        }

        // Load nested relationships if needed
        if (!empty($relationships['review'])) {
            $toLoad[] = $relationships['review'] . '.author';
        }
        
        if (!empty($relationships['event'])) {
            $toLoad[] = $relationships['event'] . '.organizer';
        }

        return array_unique($toLoad);
    }

    /**
     * Get SEO title
     */
    public function getSEOTitle(): ?string
    {
        return $this->getSEOAttribute('title');
    }

    /**
     * Get SEO description
     */
    public function getSEODescription(): ?string
    {
        return $this->getSEOAttribute('description');
    }

    /**
     * Get SEO image
     */
    public function getSEOImage(): ?string
    {
        return $this->getSEOAttribute('image');
    }

    /**
     * Get SEO keywords
     */
    public function getSEOKeywords(): array
    {
        $keywords = [];
        $relationships = $this->getSEORelationships();

        // From tags relationship
        $tagsRelationship = $relationships['tags'] ?? 'tags';
        if ($this->relationLoaded($tagsRelationship) || $this->relationExists($tagsRelationship)) {
            $tags = $this->$tagsRelationship ?? null;
            if ($tags && method_exists($tags, 'pluck')) {
                $keywords = array_merge($keywords, $tags->pluck('name')->toArray());
            }
        }

        // From categories relationship
        $categoriesRelationship = $relationships['categories'] ?? 'categories';
        if ($this->relationLoaded($categoriesRelationship) || $this->relationExists($categoriesRelationship)) {
            $categories = $this->$categoriesRelationship ?? null;
            if ($categories && method_exists($categories, 'isNotEmpty') && $categories->isNotEmpty()) {
                $keywords[] = $categories->first()->name;
            }
        }

        return array_unique($keywords);
    }

    /**
     * Get SEO author name
     */
    public function getSEOAuthor(): ?string
    {
        $relationships = $this->getSEORelationships();
        $writerRelationship = $relationships['writer'] ?? 'writer';
        $authorRelationship = $relationships['author'] ?? 'author';

        // Try writer first
        if ($this->relationLoaded($writerRelationship) || $this->relationExists($writerRelationship)) {
            $writer = $this->$writerRelationship ?? null;
            if ($writer && isset($writer->name)) {
                return $writer->name;
            }
        }

        // Try author
        if ($this->relationLoaded($authorRelationship) || $this->relationExists($authorRelationship)) {
            $author = $this->$authorRelationship ?? null;
            if ($author && isset($author->name)) {
                return $author->name;
            }
        }

        return null;
    }

    /**
     * Get SEO published date
     */
    public function getSEOPublishedAt(): ?string
    {
        $date = $this->getSEOAttribute('published_at');
        return $date ? Carbon::parse($date)->toIso8601String() : null;
    }

    /**
     * Get SEO modified date
     */
    public function getSEOModifiedAt(): ?string
    {
        $date = $this->getSEOAttribute('modified_at');
        return $date ? Carbon::parse($date)->toIso8601String() : null;
    }

    /**
     * Get FAQs for FAQ Schema
     * Override this method in your model to customize FAQ extraction
     */
    public function getSEOFAQs(): array
    {
        $relationships = $this->getSEORelationships();
        $faqsRelationship = $relationships['faqs'] ?? 'faqs';
        
        if ($this->relationLoaded($faqsRelationship) || $this->relationExists($faqsRelationship)) {
            $faqs = $this->$faqsRelationship ?? null;
            
            if ($faqs && method_exists($faqs, 'map')) {
                return $faqs->map(function($faq) {
                    return [
                        'question' => $faq->question ?? $faq->getSEOQuestion() ?? '',
                        'answer' => $faq->answer ?? $faq->getSEOAnswer() ?? '',
                    ];
                })->toArray();
            }
        }

        // Try JSON column (customize column name in your model)
        $faqsColumn = $this->getSEOFAQsColumn();
        if ($faqsColumn && isset($this->$faqsColumn)) {
            $faqs = is_string($this->$faqsColumn) 
                ? json_decode($this->$faqsColumn, true) 
                : $this->$faqsColumn;
            
            if (is_array($faqs)) {
                return $faqs;
            }
        }

        return [];
    }

    /**
     * Get FAQs JSON column name
     * Override in your model if using different column name
     */
    protected function getSEOFAQsColumn(): ?string
    {
        return 'faqs_data';
    }

    /**
     * Get HowTo steps for HowTo Schema
     * Override this method in your model to customize steps extraction
     */
    public function getSEOHowToSteps(): array
    {
        $relationships = $this->getSEORelationships();
        $stepsRelationship = $relationships['steps'] ?? 'steps';
        
        if ($this->relationLoaded($stepsRelationship) || $this->relationExists($stepsRelationship)) {
            $steps = $this->$stepsRelationship ?? null;
            
            if ($steps && method_exists($steps, 'map')) {
                $orderedSteps = method_exists($steps, 'orderBy') 
                    ? $steps->orderBy('order') 
                    : $steps;
                    
                $collection = method_exists($orderedSteps, 'get') 
                    ? $orderedSteps->get() 
                    : $orderedSteps;
                
                return $collection->map(function($step) {
                    return [
                        'name' => $step->title ?? $step->name ?? null,
                        'text' => $step->content ?? $step->text ?? $step->description ?? null,
                        'image' => $step->image ?? $step->photo ?? null,
                        'url' => $step->url ?? null,
                    ];
                })->toArray();
            }
        }

        // Try JSON column (customize column name in your model)
        $stepsColumn = $this->getSEOHowToStepsColumn();
        if ($stepsColumn && isset($this->$stepsColumn)) {
            $steps = is_string($this->$stepsColumn) 
                ? json_decode($this->$stepsColumn, true) 
                : $this->$stepsColumn;
            
            if (is_array($steps)) {
                return $steps;
            }
        }

        return [];
    }

    /**
     * Get HowTo steps JSON column name
     * Override in your model if using different column name
     */
    protected function getSEOHowToStepsColumn(): ?string
    {
        return 'howto_steps';
    }

    /**
     * Get Review data for Review Schema
     * Override this method in your model to customize review extraction
     */
    public function getSEOReview(): ?array
    {
        $relationships = $this->getSEORelationships();
        $reviewRelationship = $relationships['review'] ?? 'review';
        
        if ($this->relationLoaded($reviewRelationship) || $this->relationExists($reviewRelationship)) {
            $review = $this->$reviewRelationship ?? null;
            
            if ($review) {
                return [
                    'itemName' => $review->product_name ?? $review->item_name ?? $review->reviewed_item ?? null,
                    'ratingValue' => (float) ($review->rating ?? $review->rating_value ?? 0),
                    'bestRating' => (float) ($review->best_rating ?? 5.0),
                    'reviewBody' => $review->content ?? $review->review_body ?? $review->text ?? null,
                    'authorName' => $review->author->name ?? $review->author_name ?? null,
                    'datePublished' => $review->created_at?->toIso8601String() ?? $review->published_at?->toIso8601String(),
                ];
            }
        }

        // Try direct attributes (customize field names in your model)
        $reviewFields = $this->getSEOReviewFields();
        if ($reviewFields && (isset($this->{$reviewFields['rating']}) || isset($this->{$reviewFields['itemName']}))) {
            return [
                'itemName' => $this->{$reviewFields['itemName']} ?? null,
                'ratingValue' => (float) ($this->{$reviewFields['rating']} ?? 0),
                'bestRating' => (float) ($this->{$reviewFields['bestRating']} ?? 5.0),
                'reviewBody' => $this->{$reviewFields['reviewBody']} ?? $this->getSEODescription(),
                'authorName' => $this->getSEOAuthor(),
                'datePublished' => $this->getSEOPublishedAt(),
            ];
        }

        return null;
    }

    /**
     * Get Review field names
     * Override in your model to customize field names
     */
    protected function getSEOReviewFields(): ?array
    {
        return [
            'itemName' => 'reviewed_item',
            'rating' => 'rating',
            'bestRating' => 'best_rating',
            'reviewBody' => 'review_content',
        ];
    }

    /**
     * Get Event data for Event Schema
     * Override this method in your model to customize event extraction
     */
    public function getSEOEvent(): ?array
    {
        $relationships = $this->getSEORelationships();
        $eventRelationship = $relationships['event'] ?? 'event';
        
        if ($this->relationLoaded($eventRelationship) || $this->relationExists($eventRelationship)) {
            $event = $this->$eventRelationship ?? null;
            
            if ($event) {
                return [
                    'name' => $event->name ?? $this->getSEOTitle(),
                    'startDate' => $event->start_date?->toIso8601String() ?? $event->startDate ?? null,
                    'endDate' => $event->end_date?->toIso8601String() ?? $event->endDate ?? null,
                    'description' => $event->description ?? $this->getSEODescription(),
                    'locationName' => $event->location_name ?? $event->locationName ?? null,
                    'locationAddress' => $event->location_address ?? $event->locationAddress ?? null,
                    'image' => $event->image ?? $this->getSEOImage(),
                    'organizerName' => $event->organizer->name ?? $event->organizer_name ?? null,
                    'organizerUrl' => $event->organizer->url ?? $event->organizer_url ?? null,
                ];
            }
        }

        // Try direct attributes (customize field names in your model)
        $eventFields = $this->getSEOEventFields();
        if ($eventFields && isset($this->{$eventFields['startDate']})) {
            return [
                'name' => $this->getSEOTitle(),
                'startDate' => $this->{$eventFields['startDate']}?->toIso8601String() ?? $this->{$eventFields['startDate']},
                'endDate' => $this->{$eventFields['endDate']}?->toIso8601String() ?? $this->{$eventFields['endDate']} ?? null,
                'description' => $this->getSEODescription(),
                'locationName' => $this->{$eventFields['locationName']} ?? null,
                'locationAddress' => $this->{$eventFields['locationAddress']} ?? null,
                'image' => $this->getSEOImage(),
                'organizerName' => $this->{$eventFields['organizerName']} ?? null,
                'organizerUrl' => $this->{$eventFields['organizerUrl']} ?? null,
            ];
        }

        return null;
    }

    /**
     * Get Event field names
     * Override in your model to customize field names
     */
    protected function getSEOEventFields(): ?array
    {
        return [
            'startDate' => 'start_date',
            'endDate' => 'end_date',
            'locationName' => 'location_name',
            'locationAddress' => 'location_address',
            'organizerName' => 'organizer_name',
            'organizerUrl' => 'organizer_url',
        ];
    }

    /**
     * Get SEO attribute value
     */
    protected function getSEOAttribute(string $key): mixed
    {
        $fieldMap = $this->getSEOFieldMap();
        $fields = $fieldMap[$key] ?? [];
        
        foreach ($fields as $field) {
            if (isset($this->$field)) {
                return $this->$field;
            }
        }

        return null;
    }

    /**
     * Check if relationship exists
     */
    protected function relationExists(string $relation): bool
    {
        return method_exists($this, $relation);
    }

    /**
     * Get SEO type (post, tutorial, review, event)
     */
    public function getSEOType(): string
    {
        // Override in model to specify type
        return $this->type ?? 'post';
    }

    /**
     * Check if model has FAQs
     */
    public function hasSEOFAQs(): bool
    {
        return !empty($this->getSEOFAQs());
    }

    /**
     * Check if model has HowTo steps
     */
    public function hasSEOHowToSteps(): bool
    {
        return !empty($this->getSEOHowToSteps());
    }

    /**
     * Check if model has Review data
     */
    public function hasSEOReview(): bool
    {
        return $this->getSEOReview() !== null;
    }

    /**
     * Check if model has Event data
     */
    public function hasSEOEvent(): bool
    {
        return $this->getSEOEvent() !== null;
    }

    /**
     * Get Product data for Product Schema
     * Override this method in your model to customize product extraction
     */
    public function getSEOProduct(): ?array
    {
        // Try direct attributes (customize field names in your model)
        $productFields = $this->getSEOProductFields();
        if ($productFields && isset($this->{$productFields['price']})) {
            return [
                'sku' => $this->{$productFields['sku']} ?? null,
                'mpn' => $this->{$productFields['mpn']} ?? null,
                'gtin' => $this->{$productFields['gtin']} ?? null,
                'price' => (float) ($this->{$productFields['price']} ?? 0),
                'currency' => $this->{$productFields['currency']} ?? 'USD',
                'availability' => $this->{$productFields['availability']} ?? 'InStock',
                'condition' => $this->{$productFields['condition']} ?? 'NewCondition',
                'brand' => $this->getSEOProductBrand(),
            ];
        }

        return null;
    }

    /**
     * Get Product field names
     * Override in your model to customize field names
     */
    protected function getSEOProductFields(): ?array
    {
        return [
            'sku' => 'sku',
            'mpn' => 'mpn',
            'gtin' => 'gtin',
            'price' => 'price',
            'currency' => 'currency',
            'availability' => 'availability',
            'condition' => 'condition',
        ];
    }

    /**
     * Get Product Brand
     */
    protected function getSEOProductBrand(): ?array
    {
        $relationships = $this->getSEORelationships();
        $brandRelationship = $relationships['brand'] ?? 'brand';
        
        if ($this->relationLoaded($brandRelationship) || $this->relationExists($brandRelationship)) {
            $brand = $this->$brandRelationship ?? null;
            
            if ($brand) {
                return [
                    'name' => $brand->name ?? $brand->title ?? null,
                    'logo' => $brand->logo ?? null,
                    'url' => $brand->url ?? null,
                ];
            }
        }

        // Try direct attribute
        if (isset($this->brand) && is_string($this->brand)) {
            return [
                'name' => $this->brand,
                'logo' => null,
                'url' => null,
            ];
        }

        return null;
    }

    /**
     * Get Aggregate Rating from reviews
     */
    public function getSEOAggregateRating(): ?array
    {
        $relationships = $this->getSEORelationships();
        $reviewsRelationship = $relationships['reviews'] ?? 'reviews';
        
        if ($this->relationLoaded($reviewsRelationship) || $this->relationExists($reviewsRelationship)) {
            $reviews = $this->$reviewsRelationship ?? null;
            
            if ($reviews && method_exists($reviews, 'count') && $reviews->count() > 0) {
                $avgRating = $reviews->avg('rating') ?? $reviews->avg('rating_value') ?? 0;
                $reviewCount = $reviews->count();
                
                if ($avgRating > 0) {
                    return [
                        'ratingValue' => (float) $avgRating,
                        'ratingCount' => $reviewCount,
                        'bestRating' => 5.0,
                        'worstRating' => 1.0,
                    ];
                }
            }
        }

        return null;
    }

    /**
     * Check if model has Product data
     */
    public function hasSEOProduct(): bool
    {
        return $this->getSEOProduct() !== null;
    }

    /**
     * Check if model has Aggregate Rating
     */
    public function hasSEOAggregateRating(): bool
    {
        return $this->getSEOAggregateRating() !== null;
    }
}

