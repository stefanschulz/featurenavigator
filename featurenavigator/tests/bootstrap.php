<?php
declare(strict_types=1);
// Autoload Composer dependencies (installed in the module folder)
require_once __DIR__ . '/../vendor/autoload.php';

// Define PrestaShop version constant to satisfy module files that check it.
if (!defined('_PS_VERSION_')) {
    define('_PS_VERSION_', '8.1.0'); // dummy version for testing purposes
}

// No further bootstrap actions needed for pure unit tests.
