<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use PrestaShop\Module\FeatureNavigator\Entity\SourceOptions;

class SourceOptionsSqlTest extends TestCase
{
    /**
     * @dataProvider sqlProvider
     */
    public function testGetSql(string $letter, string $feature, string $order, int $lang, int $shop, string $expectedSqlPart): void
    {
        $sql = SourceOptions::getSql(
            $letter,
            $feature,
            $order,
            $lang,
            $shop
        );

        $this->assertStringContainsString($expectedSqlPart, $sql);
    }

    public static function sqlProvider(): array
    {
        return [
            'standard_query' => [
                'a', '1', 'ASC', 1, 1, 
                'LOWER(fvl.value) LIKE "a%"'
            ],
            'remainder_query' => [
                '#', '1', 'ASC', 1, 1, 
                'NOT (LOWER(fvl.value) LIKE "a%" OR LOWER(fvl.value) LIKE "b%"'
            ],
        ];
    }
}
