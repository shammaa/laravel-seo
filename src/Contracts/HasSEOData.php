<?php

declare(strict_types=1);

namespace Shammaa\LaravelSEO\Contracts;

interface HasSEOData
{
    /**
     * Get SEO title
     */
    public function getSEOTitle(): ?string;

    /**
     * Get SEO description
     */
    public function getSEODescription(): ?string;

    /**
     * Get SEO image
     */
    public function getSEOImage(): ?string;

    /**
     * Get SEO keywords
     */
    public function getSEOKeywords(): array;

    /**
     * Get SEO author name
     */
    public function getSEOAuthor(): ?string;

    /**
     * Get SEO published date
     */
    public function getSEOPublishedAt(): ?string;

    /**
     * Get SEO modified date
     */
    public function getSEOModifiedAt(): ?string;

    /**
     * Get FAQs for FAQ Schema
     */
    public function getSEOFAQs(): array;

    /**
     * Get HowTo steps for HowTo Schema
     */
    public function getSEOHowToSteps(): array;

    /**
     * Get Review data for Review Schema
     */
    public function getSEOReview(): ?array;

    /**
     * Get Event data for Event Schema
     */
    public function getSEOEvent(): ?array;
}

