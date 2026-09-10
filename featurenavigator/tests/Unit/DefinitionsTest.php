<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use PrestaShop\Module\FeatureNavigator\Entity\Definitions;

class DefinitionsTest extends TestCase
{
    public function testConstants(): void
    {
        $this->assertEquals('featurenavigator', Definitions::MODULE_NAME);
        $this->assertEquals('Modules.FeatureNavigator.Admin', Definitions::TRANS_ADMIN);
        $this->assertEquals('Required fields must be given.', Definitions::ERROR_MISSING_REQUIRED_FIELDS);
    }
}
