<?php
declare(strict_types=1);
// Autoload Composer dependencies (installed in the module folder)
require_once __DIR__ . '/../vendor/autoload.php';

// Define PrestaShop version constant to satisfy module files that check it.
if (!defined('_PS_VERSION_')) {
    define('_PS_VERSION_', '8.1.0'); // dummy version for testing purposes
}

// Define PrestaShop constants and mocks for unit testing.
define('_DB_PREFIX_', 'ps_');

// Mock Shop class if it doesn't exist or is not fully loaded.
if (!class_exists('Shop')) {
    class Shop {
        public static function addSqlRestrictionOnLang(string $lang) {
            return " AND id_lang = " . intval($lang);
        }
    }
}

// No further bootstrap actions needed for pure unit tests.
