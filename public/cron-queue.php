<?php
$secret = '3n72drd31ubrwluu0x90vfntm2yagm3y';
if (!isset($_GET['token']) || $_GET['token'] !== $secret) {
    http_response_code(403);
    die('Forbidden');
}
try {
    define('LARAVEL_START', microtime(true));
    require __DIR__.'/../vendor/autoload.php';
    $app = require_once __DIR__.'/../bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $status = $kernel->handle(
        $input = new Symfony\Component\Console\Input\ArrayInput([
            'command' => 'queue:work',
            '--stop-when-empty' => true,
            '--max-time' => 240
        ]),
        new Symfony\Component\Console\Output\ConsoleOutput
    );
    $kernel->terminate($input, $status);
    http_response_code(200);
    echo "OK - Queue executed with status {$status}";
} catch (Throwable $e) {
    http_response_code(500);
    echo "ERROR: " . $e->getMessage();
}
