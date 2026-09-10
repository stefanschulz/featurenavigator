<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use PrestaShop\Module\FeatureNavigator\Entity\DirectionOptions;

class DirectionOptionsTest extends TestCase
{
    public function testGetOptions(): void
    {
        $options = DirectionOptions::getOptions();
        $this->assertCount(2, $options);
        $this->assertEquals('Ascending', $options[0]->getLabel());
        $this->assertEquals('Descending', $options[1]->getLabel());
    }

    public function testGetOrDefault(): void
    {
        $this->assertEquals('Ascending', DirectionOptions::getOrDefault('unknown')->getLabel());
        $this->assertEquals('Ascending', DirectionOptions::getOrDefault('ascending')->getLabel());
        $this->assertEquals('Descending', DirectionOptions::getOrDefault('descending')->getLabel());
    }
}
