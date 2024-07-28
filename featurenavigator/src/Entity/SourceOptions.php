<?php
/**
 * Copyright 2023 Stefan Schulz
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please email schulz@the-loom.de,
 * so we can send you a copy immediately.
 *
 * @author    Stefan Schulz <schulz@the-loom.de>
 * @copyright 2023 Stefan Schulz
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */
declare(strict_types=1);

namespace PrestaShop\Module\FeatureNavigator\Entity;

if (!defined('_PS_VERSION_')) {
    exit;
}

final class SourceOptions
{
    const CONFIG = 'FEATURE_NAVIGATOR_SOURCE';
    const FIELD = 'source';
    const FIELD_LABEL = 'Source';
    const FIELD_PLACEHOLDER = 'Please, select a source';
    const REMAINDER = '#';

    public static function getDefault(): string
    {
        return '0';
    }

    public static function getSql(string $letter, string $feature, string $order, int $lang, int $shop): string
    {
        $letter = self::adjustValue($letter);
        if ($letter == self::REMAINDER) {
            $terms = [];
            foreach (range('a', 'z') as $item) {
                $terms[] = 'LOWER(§topic) LIKE "' . $item . '%"';
            }
            $query = 'NOT (' . join(' OR ', $terms) . ')';
        } else {
            $query = 'LOWER(§topic) LIKE "' . $letter . '%"';
        }

        return self::getFeatureSql($query, intval($feature), $order, $lang, $shop);
    }

    public static function adjustValue(string $letter)
    {
        $letter = strtolower($letter);
        $aToZ = range('a', 'z');
        if (in_array($letter, $aToZ, true)) {
            return $letter;
        }
        return self::REMAINDER;
    }

    private static function getFeatureSql(string $query, int $feature, string $order, int $lang, int $shop): string
    {
        $query = str_replace('§topic', 'fvl.value', $query);

        return <<<EOT
        SELECT DISTINCT fvl.value as topic
          FROM `ps_product_shop` sh
          LEFT JOIN `ps_product_lang` sl ON sh.id_product = sl.id_product
          LEFT JOIN `ps_feature_product` fp ON sh.id_product = fp.id_product
          LEFT JOIN `ps_feature_lang` fl ON fp.id_feature = fl.id_feature
          LEFT JOIN `ps_feature_value_lang` fvl ON fp.id_feature_value = fvl.id_feature_value AND fvl.id_lang = 1
          WHERE sl.id_lang = $lang
            AND sl.id_shop = $shop
            AND fl.id_lang = $lang
            AND sh.active = 1
            AND sh.visibility IN ("both", "search")
            AND sh.indexed = 1
            AND fp.id_feature = $feature
            AND $query
          ORDER BY fvl.value $order;
        EOT;
    }
}
