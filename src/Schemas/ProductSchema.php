<?php

declare(strict_types=1);

namespace Shammaa\LaravelSEO\Schemas;

use Shammaa\LaravelSEO\Data\PageData;

final class ProductSchema
{
    public function __construct(
        private array $config = []
    ) {
    }

    public function build(PageData $pageData, $model, array $siteData): array
    {
        $images = $this->buildImages($pageData->image);
        
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $pageData->title,
            'description' => $pageData->description,
            'image' => $images,
            'url' => request()->url(),
        ];

        // SKU
        if (is_object($model) && isset($model->sku)) {
            $schema['sku'] = $model->sku;
        }

        // MPN (Manufacturer Part Number)
        if (is_object($model) && isset($model->mpn)) {
            $schema['mpn'] = $model->mpn;
        }

        // GTIN (Global Trade Item Number) - UPC, EAN, ISBN
        if (is_object($model) && isset($model->gtin)) {
            $schema['gtin'] = $model->gtin;
        }

        // Brand
        if (is_object($model) && isset($model->brand)) {
            $brand = $model->brand;
            if (is_object($brand)) {
                $schema['brand'] = [
                    '@type' => 'Brand',
                    'name' => $brand->name ?? $brand->title ?? null,
                ];
                
                if (isset($brand->logo)) {
                    $schema['brand']['logo'] = $brand->logo;
                }
            } elseif (is_string($brand)) {
                $schema['brand'] = [
                    '@type' => 'Brand',
                    'name' => $brand,
                ];
            }
        }

        // Category
        if (is_object($model) && isset($model->category)) {
            $category = $model->category;
            if (is_object($category)) {
                $schema['category'] = $category->name ?? $category->title ?? null;
            } elseif (is_string($category)) {
                $schema['category'] = $category;
            }
        }

        // Aggregate Rating (from multiple reviews)
        if (is_object($model) && isset($model->reviews) && method_exists($model->reviews, 'count')) {
            $reviews = $model->reviews;
            if ($reviews->count() > 0) {
                $avgRating = $reviews->avg('rating') ?? $reviews->avg('rating_value') ?? 0;
                $reviewCount = $reviews->count();
                
                if ($avgRating > 0) {
                    $schema['aggregateRating'] = [
                        '@type' => 'AggregateRating',
                        'ratingValue' => (float) $avgRating,
                        'bestRating' => 5.0,
                        'worstRating' => 1.0,
                        'ratingCount' => $reviewCount,
                        'reviewCount' => $reviewCount,
                    ];
                }
            }
        }

        // Offer
        if (is_object($model)) {
            $offer = $this->buildOffer($model);
            if (!empty($offer)) {
                $schema['offers'] = $offer;
            }
        }

        // Additional Properties
        if (is_object($model) && isset($model->color)) {
            $schema['color'] = $model->color;
        }

        if (is_object($model) && isset($model->size)) {
            $schema['size'] = $model->size;
        }

        if (is_object($model) && isset($model->material)) {
            $schema['material'] = $model->material;
        }

        if (is_object($model) && isset($model->weight)) {
            $schema['weight'] = $model->weight;
        }

        if (is_object($model) && isset($model->height)) {
            $schema['height'] = $model->height;
        }

        if (is_object($model) && isset($model->width)) {
            $schema['width'] = $model->width;
        }

        if (is_object($model) && isset($model->depth)) {
            $schema['depth'] = $model->depth;
        }

        return $schema;
    }

    private function buildImages(?string $imagePath): array
    {
        $images = [];
        
        if (empty($imagePath)) {
            $defaultImage = $this->config['defaults']['image'] ?? null;
            if ($defaultImage) {
                $images[] = asset($defaultImage);
            }
            return $images;
        }

        if (filter_var($imagePath, FILTER_VALIDATE_URL)) {
            $images[] = $imagePath;
        } else {
            $images[] = asset($imagePath);
        }

        return $images;
    }

    private function buildOffer($model): ?array
    {
        $price = null;
        $currency = $this->config['ecommerce']['default_currency'] ?? 'USD';
        $availability = 'https://schema.org/InStock';
        $priceValidUntil = null;
        $url = request()->url();

        // Get price
        if (isset($model->price)) {
            $price = (float) $model->price;
        } elseif (isset($model->sale_price)) {
            $price = (float) $model->sale_price;
        } elseif (isset($model->current_price)) {
            $price = (float) $model->current_price;
        }

        // Get currency
        if (isset($model->currency)) {
            $currency = $model->currency;
        } elseif (isset($model->price_currency)) {
            $currency = $model->price_currency;
        }

        // Get availability
        if (isset($model->availability)) {
            $availability = $this->normalizeAvailability($model->availability);
        } elseif (isset($model->in_stock)) {
            $availability = $model->in_stock 
                ? 'https://schema.org/InStock' 
                : 'https://schema.org/OutOfStock';
        } elseif (isset($model->stock_quantity)) {
            $availability = ($model->stock_quantity > 0)
                ? 'https://schema.org/InStock'
                : 'https://schema.org/OutOfStock';
        }

        // Get price valid until
        if (isset($model->sale_end_date)) {
            $priceValidUntil = is_string($model->sale_end_date)
                ? $model->sale_end_date
                : $model->sale_end_date->format('Y-m-d');
        }

        if ($price === null) {
            return null;
        }

        $offer = [
            '@type' => 'Offer',
            'price' => number_format($price, 2, '.', ''),
            'priceCurrency' => $currency,
            'availability' => $availability,
            'url' => $url,
        ];

        // Add seller if available
        if (isset($model->seller)) {
            $seller = $model->seller;
            if (is_object($seller)) {
                $offer['seller'] = [
                    '@type' => 'Organization',
                    'name' => $seller->name ?? $seller->title ?? null,
                ];
            }
        }

        // Add price valid until
        if ($priceValidUntil) {
            $offer['priceValidUntil'] = $priceValidUntil;
        }

        // Add item condition
        if (isset($model->condition)) {
            $offer['itemCondition'] = $this->normalizeCondition($model->condition);
        }

        // Add shipping details
        if (isset($model->shipping_cost)) {
            $offer['shippingDetails'] = [
                '@type' => 'OfferShippingDetails',
                'shippingRate' => [
                    '@type' => 'MonetaryAmount',
                    'value' => (float) $model->shipping_cost,
                    'currency' => $currency,
                ],
            ];
        }

        return $offer;
    }

    private function normalizeAvailability($availability): string
    {
        if (is_string($availability)) {
            $availability = strtolower($availability);
            
            return match ($availability) {
                'in stock', 'instock', 'available', '1', 'true' => 'https://schema.org/InStock',
                'out of stock', 'outofstock', 'unavailable', '0', 'false' => 'https://schema.org/OutOfStock',
                'preorder', 'pre-order' => 'https://schema.org/PreOrder',
                'backorder', 'back-order' => 'https://schema.org/BackOrder',
                'discontinued' => 'https://schema.org/Discontinued',
                default => 'https://schema.org/InStock',
            };
        }

        return $availability ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock';
    }

    private function normalizeCondition($condition): string
    {
        if (is_string($condition)) {
            $condition = strtolower($condition);
            
            return match ($condition) {
                'new', 'brand new' => 'https://schema.org/NewCondition',
                'used', 'pre-owned' => 'https://schema.org/UsedCondition',
                'refurbished', 'refurb' => 'https://schema.org/RefurbishedCondition',
                'damaged' => 'https://schema.org/DamagedCondition',
                default => 'https://schema.org/NewCondition',
            };
        }

        return 'https://schema.org/NewCondition';
    }
}

