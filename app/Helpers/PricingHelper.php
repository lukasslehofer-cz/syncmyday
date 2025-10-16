<?php

namespace App\Helpers;

class PricingHelper
{
    /**
     * Get Stripe Price ID for locale and interval
     * 
     * @param string|null $locale
     * @param string $interval 'monthly' or 'yearly'
     * @return string|null
     */
    public static function getPriceId(?string $locale = null, string $interval = 'yearly'): ?string
    {
        $locale = $locale ?? app()->getLocale();
        $interval = in_array($interval, ['monthly', 'yearly']) ? $interval : 'yearly';
        
        // Get price ID from config based on locale and interval
        $priceId = config("services.stripe.prices_{$interval}.{$locale}");
        
        return $priceId;
    }

    /**
     * Get currency information for locale and interval
     * 
     * @param string|null $locale
     * @param string $interval 'monthly' or 'yearly'
     * @return array
     */
    public static function getCurrency(?string $locale = null, string $interval = 'yearly'): array
    {
        $locale = $locale ?? app()->getLocale();
        $interval = in_array($interval, ['monthly', 'yearly']) ? $interval : 'yearly';
        
        // Get currency from config based on locale
        $currency = config("services.stripe.currencies.{$locale}");
        
        // Fallback to CZK if not found
        if (!$currency) {
            return [
                'code' => 'CZK',
                'symbol' => 'Kč',
                'amount' => $interval === 'monthly' ? 29 : 249,
            ];
        }
        
        // Return currency with appropriate amount for interval
        return [
            'code' => $currency['code'],
            'symbol' => $currency['symbol'],
            'amount' => $currency["amount_{$interval}"] ?? 0,
        ];
    }

    /**
     * Format price with currency and interval
     * 
     * @param string|null $locale
     * @param string $interval 'monthly' or 'yearly'
     * @return string
     */
    public static function formatPrice(?string $locale = null, string $interval = 'yearly'): string
    {
        $currency = self::getCurrency($locale, $interval);
        $locale = $locale ?? app()->getLocale();
        
        // Format based on locale conventions
        switch ($locale) {
            case 'cs':
            case 'sk':
                $price = number_format($currency['amount'], 0, ',', ' ') . ' ' . $currency['symbol'];
                break;
            
            case 'pl':
                $price = number_format($currency['amount'], 0, ',', ' ') . ' ' . $currency['symbol'];
                break;
            
            case 'de':
                $price = $currency['symbol'] . ' ' . number_format($currency['amount'], 0, ',', '.');
                break;
            
            case 'en':
            default:
                $price = $currency['symbol'] . number_format($currency['amount'], 0, '.', ',');
        }
        
        return $price;
    }

    /**
     * Get price amount as float for interval
     * 
     * @param string|null $locale
     * @param string $interval 'monthly' or 'yearly'
     * @return float
     */
    public static function getAmount(?string $locale = null, string $interval = 'yearly'): float
    {
        $currency = self::getCurrency($locale, $interval);
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

    /**
     * Calculate yearly savings compared to monthly
     * 
     * @param string|null $locale
     * @return float Percentage saved
     */
    public static function getYearlySavings(?string $locale = null): float
    {
        $monthlyAmount = self::getAmount($locale, 'monthly');
        $yearlyAmount = self::getAmount($locale, 'yearly');
        
        if ($monthlyAmount <= 0) {
            return 0;
        }
        
        $monthlyYearTotal = $monthlyAmount * 12;
        $savings = (($monthlyYearTotal - $yearlyAmount) / $monthlyYearTotal) * 100;
        
        return round($savings, 0);
    }

    /**
     * Get formatted price with interval label
     * 
     * @param string|null $locale
     * @param string $interval 'monthly' or 'yearly'
     * @return string
     */
    public static function formatPriceWithInterval(?string $locale = null, string $interval = 'yearly'): string
    {
        $price = self::formatPrice($locale, $interval);
        $intervalLabel = $interval === 'monthly' ? __('messages.per_month') : __('messages.per_year');
        
        return $price . ' ' . $intervalLabel;
    }
}

