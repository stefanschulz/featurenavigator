<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use PrestaShop\Module\FeatureNavigator\Entity\HeadingOptions;

class HeadingOptionsTest extends TestCase
{
    public function testConstants(): void
    {
        $this->assertEquals('FEATURE_NAVIGATOR_HEADING', HeadingOptions::CONFIG);
        $this->assertEquals('heading', HeadingOptions::FIELD);
        $this->assertEquals('Heading', HeadingOptions::FIELD_LABEL);
        $this->assertEquals('Please, define a page heading', HeadingOptions::FIELD_PLACEHOLDER);
    }
}
