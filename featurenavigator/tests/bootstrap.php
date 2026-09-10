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

require_once __DIR__ . '/Mocks/PrestaShopMock.php';
require_once __DIR__ . '/Mocks/DataConfigurationMock.php';

// Mock Shop class if it doesn't exist or is not fully loaded.
if (!class_exists('Shop')) {
    class Shop {
        public static function addSqlRestrictionOnLang(string $lang) {
            return " AND id_lang = " . intval($lang);
        }

        public static function addSqlAssociation(string $table, string $alias): string {
            return " JOIN `" . _DB_PREFIX_ . $table . "` " . $alias . " ON (p.id_product = " . $alias . ".id_product)";
        }
    }
}

if (!class_exists('Db')) {
    class Db {
        public function executeS(string $sql, bool $multiline = false, bool $cache = false): array {
            return [];
        }

        public function getValue(string $sql, bool $multiline = false, bool $cache = false) {
            return 0;
        }
    }
}

// No further bootstrap actions needed for pure unit tests.
