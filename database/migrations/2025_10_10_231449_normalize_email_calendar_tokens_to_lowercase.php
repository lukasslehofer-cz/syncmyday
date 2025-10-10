<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Convert all email tokens to lowercase for consistency
        // This fixes case-sensitivity issues when processing incoming emails
        
        $connections = DB::table('email_calendar_connections')->get();
        
        foreach ($connections as $connection) {
            $oldToken = $connection->email_token;
            $newToken = strtolower($oldToken);
            
            // Only update if token has uppercase letters
            if ($oldToken !== $newToken) {
                // Update token
                DB::table('email_calendar_connections')
                    ->where('id', $connection->id)
                    ->update([
                        'email_token' => $newToken,
                        'email_address' => $newToken . '@' . config('app.email_domain', 'syncmyday.com'),
                    ]);
                
                \Log::info('Normalized email calendar token', [
                    'id' => $connection->id,
                    'old_token' => $oldToken,
                    'new_token' => $newToken,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Cannot reverse - we don't store the original case
        // This is a data normalization migration
    }
};
