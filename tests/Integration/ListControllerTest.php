<?php
declare(strict_types=1);

use PrestaShop\Testing\PhpUnit\TestCase as PsTestCase;
use PrestaShop\Module\FeatureNavigator\Controller\FeatureNavigatorListModuleFrontController;

class ListControllerTest extends PsTestCase
{
    public function testInitContentAssignsVariables(): void
    {
        $_GET['letter'] = 'b';
        $controller = new FeatureNavigatorListModuleFrontController();
        $controller->initContent();
        $smarty = $this->getContext()->smarty;
        $this->assertSame('b', $smarty->getTemplateVars('letter'));
        $this->assertIsArray($smarty->getTemplateVars('entries'));
    }
}
