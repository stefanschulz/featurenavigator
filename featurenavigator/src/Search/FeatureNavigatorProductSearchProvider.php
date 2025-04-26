<?php
/**
 * Copyright 2025 Stefan Schulz
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @author    Stefan Schulz <schulz@the-loom.de>
 * @copyright 2025 Stefan Schulz
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
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
