<?php

namespace App\Services\Encryption;

/**
 * Token Encryption Service
 * 
 * Uses sodium_crypto_secretbox for authenticated encryption of OAuth tokens.
 * Provides additional layer of security beyond Laravel's application key.
 * 
 * Key Management:
 * - Uses TOKEN_ENCRYPTION_KEY from .env (base64 encoded 32-byte key)
 * - Generate key with: php artisan tinker -> echo base64_encode(random_bytes(SODIUM_CRYPTO_SECRETBOX_KEYBYTES));
 * - Store key securely (environment variable, secret manager, HSM in production)
 */
class TokenEncryptionService
{
    private string $key;

    public function __construct()
    {
        $encodedKey = config('services.token_encryption_key');
        
        if (!$encodedKey) {
            throw new \RuntimeException('TOKEN_ENCRYPTION_KEY not configured');
        }

        // Remove 'base64:' prefix if present (Laravel style)
        if (str_starts_with($encodedKey, 'base64:')) {
            $encodedKey = substr($encodedKey, 7);
        }

        // Decode the base64 key
        $key = base64_decode($encodedKey, true);
        
        if ($key === false) {
            throw new \RuntimeException('TOKEN_ENCRYPTION_KEY is not valid base64');
        }
        
        if (strlen($key) !== SODIUM_CRYPTO_SECRETBOX_KEYBYTES) {
            throw new \RuntimeException('TOKEN_ENCRYPTION_KEY must be ' . SODIUM_CRYPTO_SECRETBOX_KEYBYTES . ' bytes (currently ' . strlen($key) . ' bytes)');
        }

        $this->key = $key;
    }

    /**
     * Encrypt a token
     * 
     * @param string $plaintext The token to encrypt
     * @return string Base64-encoded ciphertext with nonce prepended
     */
    public function encrypt(string $plaintext): string
    {
        // Generate a random nonce
        $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
        
        // Encrypt the plaintext
        $ciphertext = sodium_crypto_secretbox($plaintext, $nonce, $this->key);
        
        // Prepend nonce to ciphertext (we need it for decryption)
        $encrypted = $nonce . $ciphertext;
        
        // Clear sensitive data from memory
        sodium_memzero($plaintext);
        
        return base64_encode($encrypted);
    }

    /**
     * Decrypt a token
     * 
     * @param string $encrypted Base64-encoded ciphertext with nonce
     * @return string Decrypted token
     * @throws \RuntimeException If decryption fails
     */
    public function decrypt(string $encrypted): string
    {
        // Decode from base64
        $decoded = base64_decode($encrypted);
        
        if ($decoded === false) {
            throw new \RuntimeException('Invalid encrypted data');
        }
        
        // Extract nonce and ciphertext
        $nonce = mb_substr($decoded, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, '8bit');
        $ciphertext = mb_substr($decoded, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, null, '8bit');
        
        // Decrypt
        $plaintext = sodium_crypto_secretbox_open($ciphertext, $nonce, $this->key);
        
        if ($plaintext === false) {
            throw new \RuntimeException('Decryption failed - token may be corrupted');
        }
        
        return $plaintext;
    }

    /**
     * Securely generate a new encryption key
     * 
     * @return string Base64-encoded key
     */
    public static function generateKey(): string
    {
        $key = random_bytes(SODIUM_CRYPTO_SECRETBOX_KEYBYTES);
        return base64_encode($key);
    }
}

