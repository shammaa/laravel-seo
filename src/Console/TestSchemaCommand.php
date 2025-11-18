<?php

declare(strict_types=1);

namespace Shammaa\LaravelSEO\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TestSchemaCommand extends Command
{
    protected $signature = 'seo:test-schema 
                            {url? : The URL to test (defaults to current site)}
                            {--format=json : Output format (json, table)}';

    protected $description = 'Test structured data (JSON-LD) schemas using Google Rich Results Test API';

    public function handle(): int
    {
        $url = $this->argument('url') ?? config('app.url');
        $format = $this->option('format');

        $this->info("Testing schema for: {$url}");
        $this->newLine();

        try {
            // Fetch the page
            $response = Http::timeout(30)->get($url);
            
            if (!$response->successful()) {
                $this->error("Failed to fetch URL: {$url}");
                return Command::FAILURE;
            }

            $html = $response->body();
            
            // Extract JSON-LD schemas
            preg_match_all('/<script[^>]*type=["\']application\/ld\+json["\'][^>]*>(.*?)<\/script>/s', $html, $matches);
            
            if (empty($matches[1])) {
                $this->warn('No JSON-LD schemas found on this page.');
                return Command::SUCCESS;
            }

            $schemas = [];
            foreach ($matches[1] as $index => $json) {
                $schema = json_decode(trim($json), true);
                if ($schema) {
                    $schemas[] = [
                        'index' => $index + 1,
                        'type' => $schema['@type'] ?? 'Unknown',
                        'valid' => json_last_error() === JSON_ERROR_NONE,
                        'schema' => $schema,
                    ];
                }
            }

            if ($format === 'table') {
                $this->displayTable($schemas);
            } else {
                $this->displayJson($schemas);
            }

            $this->newLine();
            $this->info('💡 Tip: Test your schemas at https://search.google.com/test/rich-results');

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Error: {$e->getMessage()}");
            return Command::FAILURE;
        }
    }

    private function displayTable(array $schemas): void
    {
        $tableData = [];
        foreach ($schemas as $schema) {
            $tableData[] = [
                $schema['index'],
                $schema['type'],
                $schema['valid'] ? '✅ Valid' : '❌ Invalid',
            ];
        }

        $this->table(['#', 'Type', 'Status'], $tableData);
    }

    private function displayJson(array $schemas): void
    {
        $output = [];
        foreach ($schemas as $schema) {
            $output[] = [
                'index' => $schema['index'],
                'type' => $schema['type'],
                'valid' => $schema['valid'],
            ];
        }

        $this->line(json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
}

