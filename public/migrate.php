<?php
/**
 * Database Migration Runner (Shared Hosting Compatible)
 * 
 * Runs pending database migrations via HTTP.
 * 
 * Usage: https://syncmyday.cz/migrate.php?token=YOUR_CRON_SECRET
 * 
 * Security: Requires CRON_SECRET token to prevent unauthorized access
 */

define('LARAVEL_START', microtime(true));

// Register the Composer autoloader
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel application
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Security check - require CRON_SECRET token
$cronSecret = config('app.cron_secret');

// If accessed via HTTP, verify token
if (php_sapi_name() !== 'cli') {
    $providedToken = $_GET['token'] ?? '';
    
    if (empty($cronSecret)) {
        http_response_code(503);
        echo json_encode([
            'error' => 'CRON_SECRET not configured',
            'time' => date('Y-m-d H:i:s')
        ]);
        exit(1);
    }
    
    if (!hash_equals($cronSecret, $providedToken)) {
        http_response_code(401);
        echo json_encode([
            'error' => 'Unauthorized - invalid token',
            'time' => date('Y-m-d H:i:s')
        ]);
        exit(1);
    }
    
    header('Content-Type: application/json');
}

// Execute migrations directly
try {
    $startTime = microtime(true);
    $output = [];
    
    $output[] = '[' . date('Y-m-d H:i:s') . '] Starting database migrations...';
    
    // Get migrator instance
    $migrator = app('migrator');
    $repository = $migrator->getRepository();
    
    // Get pending migrations
    $files = $migrator->getMigrationFiles($migrator->paths());
    $ran = $repository->getRan();
    $pending = array_diff(array_keys($files), $ran);
    
    if (empty($pending)) {
        $output[] = 'Nothing to migrate - all migrations have already run';
        
        $response = [
            'status' => 'success',
            'migrated' => 0,
            'pending' => 0,
            'output' => implode("\n", $output),
            'duration' => round(microtime(true) - $startTime, 2) . 's',
            'time' => date('Y-m-d H:i:s'),
        ];
        
        echo json_encode($response, JSON_PRETTY_PRINT);
        exit(0);
    }
    
    $output[] = 'Found ' . count($pending) . ' pending migration(s)';
    
    // Run migrations
    $migrated = 0;
    $errors = 0;
    
    foreach ($pending as $migration) {
        try {
            $output[] = "Migrating: {$migration}";
            
            // Run this migration
            $migrator->run([$files[$migration]], ['pretend' => false]);
            
            $migrated++;
            $output[] = "  ✓ Migrated: {$migration}";
            
        } catch (\Exception $e) {
            $errors++;
            $output[] = "  ✗ Error: {$migration} - " . $e->getMessage();
            
            \Illuminate\Support\Facades\Log::error('Migration failed', [
                'migration' => $migration,
                'error' => $e->getMessage(),
            ]);
            
            // Stop on first error
            break;
        }
    }
    
    $output[] = "Completed: {$migrated} migrated, {$errors} errors";
    
    $response = [
        'status' => $errors > 0 ? 'partial' : 'success',
        'migrated' => $migrated,
        'errors' => $errors,
        'output' => implode("\n", $output),
        'duration' => round(microtime(true) - $startTime, 2) . 's',
        'time' => date('Y-m-d H:i:s'),
    ];
    
    if (php_sapi_name() === 'cli') {
        echo implode("\n", $output) . "\n";
    } else {
        echo json_encode($response, JSON_PRETTY_PRINT);
    }
    
    exit($errors > 0 ? 1 : 0);
    
} catch (\Exception $e) {
    $error = 'Migration error: ' . $e->getMessage();
    
    \Illuminate\Support\Facades\Log::error('Migration error', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
    ]);
    
    $response = [
        'status' => 'error',
        'error' => $error,
        'time' => date('Y-m-d H:i:s'),
    ];
    
    if (php_sapi_name() === 'cli') {
        echo $error . "\n";
    } else {
        http_response_code(500);
        echo json_encode($response, JSON_PRETTY_PRINT);
    }
    
    exit(1);
}

