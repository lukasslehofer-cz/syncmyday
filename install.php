<?php
/**
 * Jednorázový instalační skript pro sdílený hosting
 * Použití: Otevřete https://vase-domena.cz/install.php v prohlížeči
 * 
 * ⚠️  DŮLEŽITÉ: Po dokončení SMAŽTE tento soubor ze serveru!
 */

// Základní bezpečnost - změňte heslo!
define('INSTALL_PASSWORD', 'change-me-before-upload');

?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SyncMyDay - Instalace</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        .success { color: green; }
        .error { color: red; }
        .warning { color: orange; }
        pre { background: #f4f4f4; padding: 10px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>🚀 SyncMyDay - Instalace</h1>
    
    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $password = $_POST['password'] ?? '';
        
        if ($password !== INSTALL_PASSWORD) {
            echo '<p class="error">❌ Nesprávné heslo!</p>';
            exit;
        }
        
        echo '<h2>Spouštím instalaci...</h2>';
        
        try {
            require __DIR__.'/vendor/autoload.php';
            $app = require_once __DIR__.'/bootstrap/app.php';
            $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
            
            // Test připojení k databázi
            echo '<p>🔌 Testuji připojení k databázi...</p>';
            $pdo = DB::connection()->getPdo();
            echo '<p class="success">✅ Připojení k databázi úspěšné!</p>';
            
            // Spustit migrace
            echo '<p>📊 Spouštím databázové migrace...</p>';
            ob_start();
            $status = $kernel->call('migrate', ['--force' => true]);
            $output = ob_get_clean();
            
            echo '<pre>' . htmlspecialchars($output) . '</pre>';
            
            if ($status === 0) {
                echo '<p class="success">✅ Migrace dokončeny úspěšně!</p>';
            } else {
                echo '<p class="error">❌ Migrace selhaly se statusem: ' . $status . '</p>';
            }
            
            // Cache
            echo '<p>⚡ Optimalizuji cache...</p>';
            $kernel->call('config:cache');
            $kernel->call('route:cache');
            $kernel->call('view:cache');
            echo '<p class="success">✅ Cache optimalizována!</p>';
            
            echo '<hr>';
            echo '<h2 class="success">🎉 Instalace dokončena!</h2>';
            echo '<p><strong class="warning">⚠️  DŮLEŽITÉ: NYNÍ SMAŽTE soubor install.php ze serveru!</strong></p>';
            echo '<p>Můžete přejít na: <a href="/">Úvodní stránku</a></p>';
            
        } catch (Exception $e) {
            echo '<p class="error">❌ Chyba: ' . htmlspecialchars($e->getMessage()) . '</p>';
            echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
        }
    } else {
        ?>
        <p>Tento skript nainstaluje databázi a připraví aplikaci.</p>
        <p class="warning">⚠️ Před spuštěním ověřte, že:</p>
        <ul>
            <li>Všechny soubory jsou nahrány na server</li>
            <li>.env soubor je správně nakonfigurován</li>
            <li>Databáze je vytvořena</li>
            <li>Složky storage/ a bootstrap/cache/ mají oprávnění 775</li>
        </ul>
        
        <form method="POST">
            <p>
                <label>Instalační heslo: <input type="password" name="password" required></label>
            </p>
            <p class="warning">Výchozí heslo: <code>change-me-before-upload</code> (změňte před nahráním!)</p>
            <p><button type="submit">Spustit instalaci</button></p>
        </form>
        <?php
    }
    ?>
</body>
</html>
