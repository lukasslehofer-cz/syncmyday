<?php

namespace App\Helpers;

class LocaleHelper
{
    /**
     * Get domain configuration for current request
     */
    public static function getDomainConfig(): array
    {
        $host = request()->getHost();
        $domainConfigs = config('locales.domains', []);
        
        // Try exact match first
        if (isset($domainConfigs[$host])) {
            return $domainConfigs[$host];
        }
        
        // Try partial match (e.g., "syncmyday.cz" matches "www.syncmyday.cz")
        foreach ($domainConfigs as $domain => $config) {
            if (str_contains($host, $domain)) {
                return $config;
            }
        }
        
        // Return fallback if no match
        return config('locales.fallback', [
            'default' => 'en',
            'available' => ['en'],
        ]);
    }
    
    /**
     * Get available locales for current domain
     */
    public static function getAvailableLocales(): array
    {
        $domainConfig = self::getDomainConfig();
        return $domainConfig['available'] ?? ['en'];
    }
    
    /**
     * Get default locale for current domain
     */
    public static function getDefaultLocale(): string
    {
        $domainConfig = self::getDomainConfig();
        return $domainConfig['default'] ?? 'en';
    }
    
    /**
     * Get available locales with their display names for current domain
     */
    public static function getAvailableLocalesWithNames(): array
    {
        $available = self::getAvailableLocales();
        $supported = config('locales.supported', []);
        
        $result = [];
        foreach ($available as $locale) {
            if (isset($supported[$locale])) {
                $result[$locale] = $supported[$locale];
            }
        }
        
        return $result;
    }
    
    /**
     * Check if locale is available for current domain
     */
    public static function isLocaleAvailable(string $locale): bool
    {
        return in_array($locale, self::getAvailableLocales());
    }
    
    /**
     * Get all supported locales (regardless of domain)
     */
    public static function getAllSupportedLocales(): array
    {
        return config('locales.supported', []);
    }
}

