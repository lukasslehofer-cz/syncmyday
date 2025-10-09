<?php
/**
 * Scheduler endpoint pro webcron
 * Zabezpečeno tokenem
 */

$secret = 'cff5dtxlkff9mgw5479yb7gqeumagiqp';

if (!isset($_GET['token']) || $_GET['token'] !== $secret) {
    http_response_code(403);
    die('Forbidden');
}

define('LARAVEL_START', microtime(true));
require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$status = $kernel->handle(
    $input = new Symfony\Component\Console\Input\ArrayInput(['command' => 'schedule:run']),
    new Symfony\Component\Console\Output\BufferedOutput
);

$kernel->terminate($input, $status);

http_response_code(200);
echo "OK - Scheduler executed with status {$status}";