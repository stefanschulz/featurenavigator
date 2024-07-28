<?php
/**
 * Copyright 2024 Stefan Schulz
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please email schulz@the-loom.de,
 * so we can send you a copy immediately.
 *
 * @author    Stefan Schulz <schulz@the-loom.de>
 * @copyright 2024 Stefan Schulz
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */
declare(strict_types=1);

namespace PrestaShop\Module\FeatureNavigator\Form;

use PrestaShop\Module\FeatureNavigator\Entity\Definitions;
use PrestaShop\Module\FeatureNavigator\Entity\DirectionOption;
use PrestaShop\Module\FeatureNavigator\Entity\DirectionOptions;
use PrestaShop\Module\FeatureNavigator\Entity\SourceOptions;
use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;

if (!defined('_PS_VERSION_')) {
    exit;
}

final class FeatureNavigatorDataConfiguration implements DataConfigurationInterface
{
    /**
     * @var ConfigurationInterface
     */
    private ConfigurationInterface $configuration;

    /**
     * @param ConfigurationInterface $configuration
     */
    public function __construct(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    public function getConfiguration(): array
    {
        $source = $this->configuration->get(SourceOptions::CONFIG);
        $direction = $this->configuration->get(DirectionOptions::CONFIG);
        return [
            SourceOptions::FIELD => $source,
            DirectionOptions::FIELD => DirectionOptions::getOrDefault($direction),
        ];
    }

    public function updateConfiguration(array $configuration): array
    {
        $errorMessages = [];
        if ($this->validateConfiguration($configuration)) {
            /* @var $sourceOption string */
            $sourceOption = $configuration[SourceOptions::FIELD];
            $this->configuration->set(SourceOptions::CONFIG, $sourceOption);
            /* @var $sourceOption DirectionOption */
            $directionOption = $configuration[DirectionOptions::FIELD];
            $this->configuration->set(DirectionOptions::CONFIG, $directionOption->getValue());
        } else {
            $errorMessages[] = Definitions::ERROR_MISSING_REQUIRED_FIELDS;
        }
        return $errorMessages;
    }

    public function validateConfiguration(array $configuration): bool
    {
        return isset($configuration[SourceOptions::FIELD]);
    }
}
