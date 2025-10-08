<?php

namespace Tests\Unit;

use App\Services\Encryption\TokenEncryptionService;
use Tests\TestCase;

class TokenEncryptionTest extends TestCase
{
    public function test_can_encrypt_and_decrypt_token()
    {
        // Set a test encryption key
        config(['services.token_encryption_key' => base64_encode(random_bytes(32))]);

        $service = new TokenEncryptionService();
        
        $plaintext = 'my-secret-oauth-token-12345';
        $encrypted = $service->encrypt($plaintext);
        
        $this->assertNotEquals($plaintext, $encrypted);
        
        $decrypted = $service->decrypt($encrypted);
        
        $this->assertEquals($plaintext, $decrypted);
    }

    public function test_encryption_produces_different_ciphertext_each_time()
    {
        config(['services.token_encryption_key' => base64_encode(random_bytes(32))]);

        $service = new TokenEncryptionService();
        
        $plaintext = 'my-secret-token';
        $encrypted1 = $service->encrypt($plaintext);
        $encrypted2 = $service->encrypt($plaintext);
        
        // Due to random nonce, ciphertext should be different each time
        $this->assertNotEquals($encrypted1, $encrypted2);
        
        // But both should decrypt to same plaintext
        $this->assertEquals($plaintext, $service->decrypt($encrypted1));
        $this->assertEquals($plaintext, $service->decrypt($encrypted2));
    }
}

