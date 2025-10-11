<?php
/**
 * Cleanup Test Stripe Data
 * 
 * This script removes test Stripe customer IDs from the database.
 * Use this when migrating from test mode to live mode.
 * 
 * Usage: https://yourdomain.com/cleanup-stripe-test-data.php?secret=YOUR_SECRET_KEY
 * 
 * IMPORTANT: Delete this file after use for security!
 */

// Security: Set your secret key here
define('CLEANUP_SECRET', 'change-this-to-random-string-123456');

// Check secret
if (!isset($_GET['secret']) || $_GET['secret'] !== CLEANUP_SECRET) {
    http_response_code(403);
    die('Access denied. Invalid secret key.');
}

// Bootstrap Laravel
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Get confirmation
$confirm = $_GET['confirm'] ?? 'no';

?>
<!DOCTYPE html>
<html>
<head>
    <title>Cleanup Stripe Test Data</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        .warning { background: #fff3cd; border: 2px solid #ffc107; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .success { background: #d4edda; border: 2px solid #28a745; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .error { background: #f8d7da; border: 2px solid #dc3545; padding: 15px; border-radius: 5px; margin: 20px 0; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .btn { display: inline-block; padding: 10px 20px; margin: 10px 5px; text-decoration: none; border-radius: 5px; }
        .btn-danger { background: #dc3545; color: white; }
        .btn-secondary { background: #6c757d; color: white; }
    </style>
</head>
<body>
    <h1>🧹 Cleanup Stripe Test Data</h1>
    
    <div class="warning">
        <strong>⚠️ Warning:</strong> This will remove all Stripe customer and subscription IDs from your database.
        Users will need to re-enter their payment information.
    </div>

<?php
// Get users with Stripe data
$users = DB::table('users')
    ->whereNotNull('stripe_customer_id')
    ->orWhereNotNull('stripe_subscription_id')
    ->get();

if ($users->isEmpty()) {
    echo '<div class="success">';
    echo '<strong>✓ All clean!</strong> No users with Stripe data found.';
    echo '</div>';
    echo '<p><a href="/" class="btn btn-secondary">← Back to Home</a></p>';
} elseif ($confirm === 'yes') {
    // Perform cleanup
    echo '<h2>🔄 Cleaning up...</h2>';
    
    $cleaned = 0;
    foreach ($users as $user) {
        DB::table('users')
            ->where('id', $user->id)
            ->update([
                'stripe_customer_id' => null,
                'stripe_subscription_id' => null,
            ]);
        $cleaned++;
        echo "<p>✓ Cleaned user #{$user->id} ({$user->email})</p>";
    }
    
    echo '<div class="success">';
    echo "<strong>✓ Success!</strong> Cleaned {$cleaned} users.";
    echo '</div>';
    
    echo '<div class="warning">';
    echo '<strong>🔒 Security Notice:</strong> Please delete this file now!<br>';
    echo 'File location: <code>public/cleanup-stripe-test-data.php</code>';
    echo '</div>';
    
    echo '<p><a href="/" class="btn btn-secondary">← Back to Home</a></p>';
} else {
    // Show preview
    echo '<h2>📋 Users with Stripe Data</h2>';
    echo '<p>Found <strong>' . count($users) . '</strong> users with Stripe data:</p>';
    
    echo '<table>';
    echo '<tr><th>ID</th><th>Email</th><th>Customer ID</th><th>Subscription ID</th></tr>';
    foreach ($users as $user) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($user->id) . '</td>';
        echo '<td>' . htmlspecialchars($user->email) . '</td>';
        echo '<td><code>' . htmlspecialchars($user->stripe_customer_id ?? 'N/A') . '</code></td>';
        echo '<td><code>' . htmlspecialchars($user->stripe_subscription_id ?? 'N/A') . '</code></td>';
        echo '</tr>';
    }
    echo '</table>';
    
    echo '<h2>⚠️ Are you sure?</h2>';
    echo '<p>This action will:</p>';
    echo '<ul>';
    echo '<li>Remove all <code>stripe_customer_id</code> values</li>';
    echo '<li>Remove all <code>stripe_subscription_id</code> values</li>';
    echo '<li>Users will need to re-enter payment information</li>';
    echo '<li><strong>This cannot be undone!</strong></li>';
    echo '</ul>';
    
    $currentUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    echo '<p>';
    echo '<a href="' . htmlspecialchars($currentUrl) . '&confirm=yes" class="btn btn-danger">Yes, Clean Up Now</a>';
    echo '<a href="/" class="btn btn-secondary">Cancel</a>';
    echo '</p>';
}
?>

    <hr style="margin-top: 40px;">
    <p style="color: #666; font-size: 12px;">
        <strong>Security:</strong> Delete this file after use!<br>
        File: <code>public/cleanup-stripe-test-data.php</code>
    </p>
</body>
</html>

