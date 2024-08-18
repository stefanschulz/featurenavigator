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
    const FIELD_LABEL = 'Feature';
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

    public static function getAllSql(string $feature, $lang, int $shop): string
    {
        return self::getFeatureSql('§topic <> ""', intval($feature), 'ASC', $lang, $shop);
    }

    private static function getFeatureSql(string $query, int $feature, string $order, int $lang, int $shop): string
    {
        $dbPrefix = _DB_PREFIX_;
        $query = str_replace('§topic', 'fvl.value', $query);
        $sqlRestrictionOnLang = \Shop::addSqlRestrictionOnLang('pl');

        return <<<EOT
        SELECT DISTINCT fvl.value as topic
          FROM `{$dbPrefix}product_shop` product_shop
          LEFT JOIN `{$dbPrefix}product_lang` pl ON (product_shop.id_product = pl.id_product {$sqlRestrictionOnLang})
          LEFT JOIN `{$dbPrefix}feature_product` fp ON product_shop.id_product = fp.id_product
          LEFT JOIN `{$dbPrefix}feature_lang` fl ON fp.id_feature = fl.id_feature
          LEFT JOIN `{$dbPrefix}feature_value_lang` fvl ON fp.id_feature_value = fvl.id_feature_value
          WHERE pl.id_shop = $shop
            AND fl.id_lang = $lang
            AND fvl.id_lang = $lang
            AND product_shop.active = 1
            AND product_shop.visibility IN ("both", "search")
            AND product_shop.indexed = 1
            AND fp.id_feature = '$feature'
            AND $query
          ORDER BY fvl.value $order;
        EOT;
    }

    public static function getProductSql(string $featureValue, string $feature, int $lang, int $shop, string $selector, int $limit = 0, int $offset = 0): string
    {
        $dbPrefix = _DB_PREFIX_;
        $sqlRestrictionOnLang = \Shop::addSqlRestrictionOnLang('pl');
        $sqlAssociation = \Shop::addSqlAssociation('product', 'p');
        $limitation = $limit > 0 ? 'LIMIT ' . ($offset ? $offset . ', ' : '') . $limit : '';

        return <<<EOT
        SELECT $selector
          FROM `{$dbPrefix}product` p
          {$sqlAssociation}
          LEFT JOIN `{$dbPrefix}product_lang` pl ON (pl.id_product = p.id_product {$sqlRestrictionOnLang})
          LEFT JOIN `{$dbPrefix}feature_product` fp ON fp.id_product = p.id_product
          LEFT JOIN `{$dbPrefix}feature_value_lang` fvl ON fp.id_feature_value = fvl.id_feature_value
          WHERE pl.id_lang = $lang
            AND pl.id_shop = $shop
            AND fvl.id_lang = $lang
            AND product_shop.active = 1
            AND product_shop.visibility IN ("both", "search")
            AND product_shop.indexed = 1
            AND fp.id_feature = '$feature'
            AND fvl.value LIKE '%$featureValue%'
          ORDER BY pl.name ASC
          $limitation;
        EOT;
    }
}
