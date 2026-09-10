<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use PrestaShop\Module\FeatureNavigator\Entity\SourceOptions;

class SourceOptionsTest extends TestCase
{
    /** @dataProvider letters */
    public function testAdjustValue(string $input, string $expected): void
    {
        $this->assertSame($expected, SourceOptions::adjustValue($input));
    }

    public static function letters(): array
    {
        return [
            ['A', 'a'],
            ['z', 'z'],
            ['1', '#'],
            ['',  '#'],
        ];
    }
}
