<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use PrestaShop\Module\FeatureNavigator\Entity\SourceOptions;

class GetSqlTest extends TestCase
{
    public function testGetSql(): void
    {
        $sql = SourceOptions::getSql('a', '1', 'ASC', 1, 1);
        $this->assertIsString($sql);
    }
}
