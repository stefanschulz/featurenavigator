<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use PrestaShop\Module\FeatureNavigator\Search\FeatureNavigatorProductSearchProvider;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchContext;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchResult;

class FeatureNavigatorSearchTest extends TestCase
{
    private $db;
    private FeatureNavigatorProductSearchProvider $provider;

    protected function setUp(): void
    {
        $this->db = $this->createMock(\Db::class);
        $this->provider = new FeatureNavigatorProductSearchProvider($this->db);
    }

    public function testRunQueryReturnsCorrectResults(): void
    {
        $products = [
            ['id_product' => 10, 'name' => 'Product 1'],
            ['id_product' => 11, 'name' => 'Product 2'],
        ];

        // We expect two calls to the database: one for products (executeS) and one for count (getValue)
        $this->db->expects($this->once())
            ->method('executeS')
            ->willReturn($products);

        $this->db->expects($this->once())
            ->method('getValue')
            ->willReturn(2);

        $context = $this->createMock(ProductSearchContext::class);
        $context->method('getIdLang')->willReturn(1);
        $context->method('getIdShop')->willReturn(1);

        $query = $this->createMock(ProductSearchQuery::class);
        $query->method('getSearchString')->willReturn('blue');
        $query->method('getSearchTag')->willReturn('feature');
        $query->method('getResultsPerPage')->willReturn(10);
        $query->method('getPage')->willReturn(1);

        $result = $this->provider->runQuery($context, $query);

        $this->assertCount(2, $result->getProducts());
        $this->assertEquals(2, $result->getTotalProductsCount());
    }
}
