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

namespace PrestaShop\Module\FeatureNavigator\Search;

use PrestaShop\Module\FeatureNavigator\Entity\SourceOptions;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchContext;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchProviderInterface;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchResult;

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Provider for feature based product searches.
 */
class FeatureNavigatorProductSearchProvider implements ProductSearchProviderInterface
{
    private \Db $db;

    public function __construct(
        \Db $db
    ) {
        $this->db = $db;
    }

    /**
     * @throws \PrestaShopDatabaseException
     */
    public function runQuery(ProductSearchContext $context, ProductSearchQuery $query): ProductSearchResult
    {
        $result = new ProductSearchResult();
        $result->setProducts($this->getProducts($context, $query));
        $result->setTotalProductsCount($this->getCount($context, $query));
        return $result;
    }

    /**
     * @throws \PrestaShopDatabaseException
     */
    private function getProducts(ProductSearchContext $context, ProductSearchQuery $query): array
    {
        $selector = [
            'p.*',
            'product_shop.*',
            'pl.`description`',
            'pl.`description_short`',
            'pl.`link_rewrite`',
            'pl.`meta_description`',
            'pl.`meta_keywords`',
            'pl.`meta_title`',
            'pl.`name`',
            'pl.`available_now`',
            'pl.`available_later`',
        ];
        $sql = SourceOptions::getProductSql(
            $query->getSearchString(),
            $query->getSearchTag(),
            $context->getIdLang(),
            $context->getIdShop(),
            join(',', $selector),
            $query->getResultsPerPage(),
            ($query->getPage() - 1) * $query->getResultsPerPage()
        );
        if (empty($sql)) {
            return [];
        }

        return $this->db->executeS($sql, true, false);
    }

    private function getCount(ProductSearchContext $context, ProductSearchQuery $query): int
    {
        $sql = SourceOptions::getProductSql(
            $query->getSearchString(),
            $query->getSearchTag(),
            $context->getIdLang(),
            $context->getIdShop(),
            'COUNT(p.id_product)'
        );
        if (empty($sql)) {
            return 0;
        }

        return (int) $this->db->getValue($sql, true, false);
    }
}
