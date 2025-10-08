<?php
/**
 * Redirect to public folder
 * This file redirects all requests to the public directory
 * where the Laravel application is located.
 */

// Redirect to public directory
header('Location: /public/index.php' . (isset($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI'] !== '/' ? $_SERVER['REQUEST_URI'] : ''));
exit;

