<?php

namespace App\Helpers;

use App\Models\User;
use Illuminate\Support\Facades\Log;

/**
 * Email Helper
 * 
 * Handles multi-domain email configuration for SyncMyDay.
 * Each user's emails are sent from their registration domain.
 */
class EmailHelper 
{
    /**
     * Domain to locale mapping
     */
    private static array $domainToLocale = [
        'syncmyday.cz' => 'cs',
        'syncmyday.sk' => 'sk',
        'syncmyday.pl' => 'pl',
        'syncmyday.de' => 'de',
        'syncmyday.eu' => 'en',
    ];

    /**
     * Locale to domain mapping (reverse)
     */
    private static array $localeToDomain = [
        'cs' => 'syncmyday.cz',
        'sk' => 'syncmyday.sk',
        'pl' => 'syncmyday.pl',
        'de' => 'syncmyday.de',
        'en' => 'syncmyday.eu',
    ];

    /**
     * Get FROM address and configuration for user based on their registration domain
     * 
     * @param User $user The user to send email to
     * @param string $type Email type: 'info' (system emails) or 'events' (calendar blockers)
     * @return array ['address' => 'info@syncmyday.pl', 'name' => 'SyncMyDay', 'domain' => 'syncmyday.pl', 'mailer' => 'mxroute']
     */
    public static function getEmailConfig(User $user, string $type = 'info'): array
    {
        // Get domain from user's registration_domain or derive from locale
        $domain = $user->registration_domain ?? self::getDomainFromLocale($user->locale ?? 'en');
        
        // Get email prefix and mailer based on type
        if ($type === 'events') {
            // Calendar blockers → MXroute (was Mailgun before migration)
            $prefix = 'events';
            $mailer = 'mxroute';
        } else {
            // System emails → MXroute (critical, low volume)
            $prefix = 'info';
            $mailer = 'mxroute';
        }
        
        $config = [
            'address' => "{$prefix}@{$domain}",
            'name' => config('mail.from.name', 'SyncMyDay'),
            'domain' => $domain,
            'type' => $type,
            'mailer' => $mailer,
        ];
        
        // Log for audit purposes
        Log::channel('stack')->debug('Email config selected', [
            'user_id' => $user->id,
            'user_locale' => $user->locale,
            'registration_domain' => $user->registration_domain,
            'from_address' => $config['address'],
            'type' => $type,
            'mailer' => $mailer,
        ]);
        
        return $config;
    }
    
    /**
     * Get current request domain (for registration)
     * 
     * @return string Domain name (e.g., 'syncmyday.pl')
     */
    public static function getCurrentDomain(): string
    {
        if (!request()) {
            return 'syncmyday.cz'; // Fallback for CLI/queue
        }
        
        $host = request()->getHost();
        
        // Remove www. prefix if present
        $host = preg_replace('/^www\./', '', $host);
        
        // If it's one of our known domains, return it
        if (array_key_exists($host, self::$domainToLocale)) {
            return $host;
        }
        
        // Fallback to .cz
        return 'syncmyday.cz';
    }
    
    /**
     * Get domain from locale
     * 
     * @param string $locale Locale code (cs, sk, pl, de, en)
     * @return string Domain name
     */
    public static function getDomainFromLocale(string $locale): string
    {
        return self::$localeToDomain[$locale] ?? 'syncmyday.cz';
    }
    
    /**
     * Get locale from domain
     * 
     * @param string $domain Domain name
     * @return string Locale code
     */
    public static function getLocaleFromDomain(string $domain): string
    {
        // Remove www. prefix if present
        $domain = preg_replace('/^www\./', '', $domain);
        
        return self::$domainToLocale[$domain] ?? 'en';
    }
    
    /**
     * Get all supported domains
     * 
     * @return array Array of domain names
     */
    public static function getAllDomains(): array
    {
        return array_keys(self::$domainToLocale);
    }
    
    /**
     * Check if domain is valid
     * 
     * @param string $domain Domain to check
     * @return bool True if domain is supported
     */
    public static function isValidDomain(string $domain): bool
    {
        // Remove www. prefix if present
        $domain = preg_replace('/^www\./', '', $domain);
        
        return array_key_exists($domain, self::$domainToLocale);
    }
}

