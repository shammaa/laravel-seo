<?php

declare(strict_types=1);

namespace Shammaa\LaravelSEO\Console;

use Illuminate\Console\Command;

class HealthCheckCommand extends Command
{
    protected $signature = 'seo:health-check';

    protected $description = 'Check SEO configuration health and provide recommendations';

    public function handle(): int
    {
        $this->info('🔍 SEO Health Check');
        $this->newLine();

        $issues = [];
        $warnings = [];
        $success = [];

        // Check site configuration
        $siteName = config('seo.site.name');
        $siteDescription = config('seo.site.description');
        $siteUrl = config('seo.site.url');

        if (empty($siteName) || $siteName === config('app.name')) {
            $warnings[] = 'Site name not configured or using default app name';
        } else {
            $success[] = 'Site name configured';
        }

        if (empty($siteDescription)) {
            $issues[] = 'Site description is missing';
        } else {
            $success[] = 'Site description configured';
        }

        if (empty($siteUrl) || $siteUrl === url('/')) {
            $warnings[] = 'Site URL not configured or using default';
        } else {
            $success[] = 'Site URL configured';
        }

        // Check social media
        $twitterSite = config('seo.social.twitter.site');
        if (empty($twitterSite)) {
            $warnings[] = 'Twitter site handle not configured';
        } else {
            $success[] = 'Twitter site configured';
        }

        // Check analytics
        $ga4 = config('seo.analytics.ga4.measurement_id');
        $gtm = config('seo.analytics.gtm.container_id');
        if (empty($ga4) && empty($gtm)) {
            $warnings[] = 'No analytics configured (GA4 or GTM)';
        } else {
            $success[] = 'Analytics configured';
        }

        // Check multilingual
        $multilingualEnabled = config('seo.multilingual.enabled');
        if ($multilingualEnabled && empty(config('seo.multilingual.locales'))) {
            $issues[] = 'Multilingual enabled but no locales configured';
        } elseif ($multilingualEnabled) {
            $success[] = 'Multilingual configured';
        }

        // Check organization schema
        $orgName = config('seo.organization.name');
        if (empty($orgName)) {
            $warnings[] = 'Organization name not configured';
        } else {
            $success[] = 'Organization schema configured';
        }

        // Display results
        if (!empty($success)) {
            $this->info('✅ Success:');
            foreach ($success as $item) {
                $this->line("   • {$item}");
            }
            $this->newLine();
        }

        if (!empty($warnings)) {
            $this->warn('⚠️  Warnings:');
            foreach ($warnings as $item) {
                $this->line("   • {$item}");
            }
            $this->newLine();
        }

        if (!empty($issues)) {
            $this->error('❌ Issues:');
            foreach ($issues as $item) {
                $this->line("   • {$item}");
            }
            $this->newLine();
        }

        // Summary
        $totalChecks = count($success) + count($warnings) + count($issues);
        $score = count($success) / max($totalChecks, 1) * 100;

        $this->info("📊 Health Score: " . round($score) . "%");
        $this->newLine();

        if ($score >= 80) {
            $this->info('🎉 Excellent! Your SEO configuration looks good.');
        } elseif ($score >= 60) {
            $this->warn('⚠️  Good, but there\'s room for improvement.');
        } else {
            $this->error('❌ Your SEO configuration needs attention.');
        }

        return Command::SUCCESS;
    }
}

