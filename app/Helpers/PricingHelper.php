<?php

namespace App\Helpers;

class PricingHelper
{
    /**
     * Get Stripe Price ID for current locale
     */
    public static function getPriceId(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        
        // Get price ID from config based on locale
        $priceId = config("services.stripe.prices.{$locale}");
        
        // Fallback to default if not found
        return $priceId ?? config('services.stripe.pro_price_id');
    }

    /**
     * Get currency information for current locale
     */
    public static function getCurrency(?string $locale = null): array
    {
        $locale = $locale ?? app()->getLocale();
        
        // Get currency from config based on locale
        $currency = config("services.stripe.currencies.{$locale}");
        
        // Fallback to CZK if not found
        return $currency ?? [
            'code' => 'CZK',
            'symbol' => 'Kč',
            'amount' => 249,
        ];
    }

    /**
     * Format price with currency
     */
    public static function formatPrice(?string $locale = null): string
    {
        $currency = self::getCurrency($locale);
        
        // Format based on locale conventions
        switch ($locale ?? app()->getLocale()) {
            case 'cs':
            case 'sk':
                return number_format($currency['amount'], 0, ',', ' ') . ' ' . $currency['symbol'];
            
            case 'pl':
                return number_format($currency['amount'], 2, ',', ' ') . ' ' . $currency['symbol'];
            
            case 'de':
                return $currency['symbol'] . ' ' . number_format($currency['amount'], 2, ',', '.');
            
            case 'en':
            default:
                return $currency['symbol'] . number_format($currency['amount'], 2, '.', ',');
        }
    }

    /**
     * Get price amount as float
     */
    public static function getAmount(?string $locale = null): float
    {
        $currency = self::getCurrency($locale);
        return (float) $currency['amount'];
    }

    /**
     * Get currency code (CZK, EUR, USD, etc.)
     */
    public static function getCurrencyCode(?string $locale = null): string
    {
        $currency = self::getCurrency($locale);
        return $currency['code'];
    }
}

