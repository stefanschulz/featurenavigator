<?php
declare(strict_types=1);
// Composer autoloader – always present after `composer install`
require_once __DIR__ . '/../vendor/autoload.php';

// Locate the PrestaShop installation (the module lives inside a shop).
$psRoot = realpath(__DIR__ . '/../..'); // <shop_root>
if (!$psRoot || !file_exists($psRoot . '/config/config.inc.php')) {
    fwrite(STDERR, "Cannot find PrestaShop config.inc.php – aborting tests.\n");
    exit(1);
}
require_once $psRoot . '/config/config.inc.php';

// Force development mode to avoid caching side‑effects.
if (!defined('_PS_MODE_DEV_')) {
    define('_PS_MODE_DEV_', true);
}

// Load the module entry point so its PSR‑4 namespace is registered.
require_once __DIR__ . '/../featurenavigator.php';
