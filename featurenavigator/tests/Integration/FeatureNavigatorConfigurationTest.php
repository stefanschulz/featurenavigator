<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use PrestaShop\Module\FeatureNavigator\Form\FeatureNavigatorFormDataProvider;
use PrestaShop\Module\FeatureNavigator\Form\FeatureNavigatorDataConfiguration;
use PrestaShop\Module\FeatureNavigator\Entity\DirectionOption;
use PrestaShop\Module\FeatureNavigator\Entity\DirectionOptions;
use PrestaShop\Module\FeatureNavigator\Entity\HeadingOptions;
use PrestaShop\Module\FeatureNavigator\Entity\SourceOptions;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;

class FeatureNavigatorConfigurationTest extends TestCase
{
    private $mockConfig;
    private FeatureNavigatorDataConfiguration $dataConfig;
    private FeatureNavigatorFormDataProvider $provider;

    protected function setUp(): void
    {
        $this->mockConfig = $this->createMock(ConfigurationInterface::class);
        $this->dataConfig = new FeatureNavigatorDataConfiguration($this->mockConfig);
        $this->provider = new FeatureNavigatorFormDataProvider($this->dataConfig);
    }

    public function testGetConfigurationThroughProvider(): void
    {
        $this->mockConfig->expects($this->any())
            ->method('get')
            ->willReturnMap([
                [HeadingOptions::CONFIG, 'My Heading'],
                [SourceOptions::CONFIG, 'some-feature'],
                [DirectionOptions::CONFIG, 'ascending'],
            ]);

        $data = $this->provider->getData();

        $this->assertEquals('My Heading', $data[HeadingOptions::FIELD]);
        $this->assertEquals('some-feature', $data[SourceOptions::FIELD]);
    }

    public function testSetDataThroughProvider(): void
    {
        $directionOption = DirectionOptions::ascending();
        
        $configData = [
            HeadingOptions::FIELD => 'New Heading',
            SourceOptions::FIELD => 'new-feature',
            DirectionOptions::FIELD => $directionOption,
        ];

        // We expect the provider to call updateConfiguration on dataConfig, 
        // which in turn calls set on mockConfig.
        $this->mockConfig->expects($this->exactly(3))
            ->method('set')
            ->withConsecutive(
                [HeadingOptions::CONFIG, 'New Heading'],
                [SourceOptions::CONFIG, 'new-feature'],
                [DirectionOptions::CONFIG, 'ascending']
            );

        $result = $this->provider->setData($configData);
        
        // The provider returns the result of updateConfiguration which is an array of errors.
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }
}
