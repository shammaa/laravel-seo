<?php

declare(strict_types=1);

namespace Shammaa\LaravelSEO\Helpers;

final class ReadingTimeCalculator
{
    /**
     * Calculate reading time in minutes
     */
    public static function calculate(string $content, int $wordsPerMinute = 200): int
    {
        $text = strip_tags($content);
        $text = preg_replace('/\s+/', ' ', $text);
        $text = trim($text);
        
        if (empty($text)) {
            return 0;
        }
        
        $words = preg_split('/\s+/u', $text, -1, PREG_SPLIT_NO_EMPTY);
        $wordCount = count($words);
        
        $minutes = (int) ceil($wordCount / $wordsPerMinute);
        
        return max(1, $minutes); // At least 1 minute
    }

    /**
     * Calculate reading time in ISO 8601 duration format (PT5M)
     */
    public static function toIso8601(string $content, int $wordsPerMinute = 200): string
    {
        $minutes = self::calculate($content, $wordsPerMinute);
        return 'PT' . $minutes . 'M';
    }

    /**
     * Format reading time as human-readable string
     * 
     * @param string $content
     * @param int $wordsPerMinute
     * @param string $locale
     * @param array|null $translations Optional translations array from config
     * @return string
     */
    public static function format(string $content, int $wordsPerMinute = 200, string $locale = 'en', ?array $translations = null): string
    {
        $minutes = self::calculate($content, $wordsPerMinute);
        
        // Use translations from config if provided
        if ($translations !== null && isset($translations[$locale])) {
            return str_replace(':minutes', (string) $minutes, $translations[$locale]);
        }
        
        // Fallback to default translations
        $defaultTranslations = [
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
        ];
        
        $template = $defaultTranslations[$locale] ?? $defaultTranslations['en'];
        
        return str_replace(':minutes', (string) $minutes, $template);
    }
}

