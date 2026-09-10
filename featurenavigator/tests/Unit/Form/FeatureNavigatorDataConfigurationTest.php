<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use PrestaShop\Module\FeatureNavigator\Form\FeatureNavigatorDataConfiguration;
use PrestaShop\Module\FeatureNavigator\Entity\DirectionOption;
use PrestaShop\Module\FeatureNavigator\Entity\DirectionOptions;
use PrestaShop\Module\FeatureNavigator\Entity\HeadingOptions;
use PrestaShop\Module\FeatureNavigator\Entity\SourceOptions;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;

class FeatureNavigatorDataConfigurationTest extends TestCase
{
    private $mockConfig;
    private FeatureNavigatorDataConfiguration $dataConfig;

    protected function setUp(): void
    {
        $this->mockConfig = $this->createMock(ConfigurationInterface::class);
        $this->dataConfig = new FeatureNavigatorDataConfiguration($this->mockConfig);
    }

    public function testGetConfigurationReturnsCorrectMapping(): void
    {
        $this->mockConfig->expects($this->any())
            ->method('get')
            ->willReturnMap([
                [HeadingOptions::CONFIG, 'My Heading'],
                [SourceOptions::CONFIG, 'some-feature'],
                [DirectionOptions::CONFIG, 'ascending'],
            ]);

        $result = $this->dataConfig->getConfiguration();

        $this->assertEquals('My Heading', $result[HeadingOptions::FIELD]);
        $this->assertEquals('some-feature', $result[SourceOptions::FIELD]);
        $this->assertInstanceOf(DirectionOption::class, $result[DirectionOptions::FIELD]);
        $this->assertEquals('ascending', $result[DirectionOptions::FIELD]->getValue());
    }

    public function testUpdateConfigurationWithValidData(): void
    {
        $directionOption = DirectionOptions::ascending();
        
        $configData = [
            HeadingOptions::FIELD => 'New Heading',
            SourceOptions::FIELD => 'new-feature',
            DirectionOptions::FIELD => $directionOption,
        ];

        $this->mockConfig->expects($this->exactly(3))
            ->method('set')
            ->withConsecutive(
                [HeadingOptions::CONFIG, 'New Heading'],
                [SourceOptions::CONFIG, 'new-feature'],
                [DirectionOptions::CONFIG, 'ascending']
            );

        $errors = $this->dataConfig->updateConfiguration($configData);
        $this->assertEmpty($errors);
    }

    public function testUpdateConfigurationWithMissingFieldsReturnsError(): void
    {
        $configData = [
            HeadingOptions::FIELD => 'New Heading',
            // Missing SourceOptions::FIELD
        ];

        $this->mockConfig->expects($this->never())->method('set');

        $errors = $this->dataConfig->updateConfiguration($configData);
        $this->assertNotEmpty($errors);
        $this->assertContains('Required fields must be given.', $errors);
    }
}
