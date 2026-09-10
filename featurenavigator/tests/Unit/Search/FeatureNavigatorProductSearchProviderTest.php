<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use PrestaShop\Module\FeatureNavigator\Search\FeatureNavigatorProductSearchProvider;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchContext;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchResult;

class FeatureNavigatorProductSearchProviderTest extends TestCase
{
    /**
     * @dataProvider queryProvider
     */
    public function testRunQuery(string $searchString, string $searchTag, int $lang, int $shop, int $resultsPerPage, int $page, array $expectedProducts, int $expectedCount): void
    {
        $db = $this->createMock(\Db::class);
        
        // Mock executeS for getProducts
        $db->expects($this->once())
            ->method('executeS')
            ->with(
                $this->callback(function ($sql) use ($searchString, $searchTag, $lang, $shop, $resultsPerPage, $page) {
                    // Verify the SQL contains expected parts. 
                    // This is a bit complex because SourceOptions::getProductSql is called internally.
                    return str_contains($sql, "LIKE '%$searchString%'");
                }),
                true,
                false
            )
            ->willReturn($expectedProducts);

        // Mock getValue for getCount
        $db->expects($this->once())
            ->method('getValue')
            ->with(
                $this->callback(function ($sql) use ($searchString, $searchTag, $lang, $shop) {
                    return str_contains($sql, "COUNT(p.id_product)");
                }),
                true,
                false
            )
            ->willReturn($expectedCount);

        $context = $this->createMock(ProductSearchContext::class);
        $context->method('getIdLang')->willReturn($lang);
        $context->method('getIdShop')->willReturn($shop);

        $query = $this->createMock(ProductSearchQuery::class);
        $query->method('getSearchString')->willReturn($searchString);
        $query->method('getSearchTag')->willReturn($searchTag);
        $query->method('getResultsPerPage')->willReturn($resultsPerPage);
        $query->method('getPage')->willReturn($page);

        $provider = new FeatureNavigatorProductSearchProvider($db);
        $result = $provider->runQuery($context, $query);

        $this->assertEquals($expectedProducts, $result->getProducts());
        $this->assertEquals($expectedCount, $result->getTotalProductsCount());
    }

    public static function queryProvider(): array
    {
        return [
            'simple_search' => [
                'blue',
                'feature',
                1,
                1,
                10,
                1,
                [['id_product' => 1, 'name' => 'Blue Product']],
                1
            ],
        ];
    }
}
