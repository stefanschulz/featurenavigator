<?php
/**
 * Copyright 2025 Stefan Schulz
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @author    Stefan Schulz <schulz@the-loom.de>
 * @copyright 2025 Stefan Schulz
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 */
declare(strict_types=1);

namespace PrestaShop\Module\FeatureNavigator\Form;

use PrestaShop\Module\FeatureNavigator\Entity\Definitions;
use PrestaShop\Module\FeatureNavigator\Entity\DirectionOption;
use PrestaShop\Module\FeatureNavigator\Entity\DirectionOptions;
use PrestaShop\Module\FeatureNavigator\Entity\HeadingOptions;
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
        $headings = $this->configuration->get(HeadingOptions::CONFIG);
        $source = $this->configuration->get(SourceOptions::CONFIG);
        $direction = $this->configuration->get(DirectionOptions::CONFIG);
        return [
            HeadingOptions::FIELD => $headings,
            SourceOptions::FIELD => $source,
            DirectionOptions::FIELD => DirectionOptions::getOrDefault($direction),
        ];
    }

    public function updateConfiguration(array $configuration): array
    {
        $errorMessages = [];
        if ($this->validateConfiguration($configuration)) {
            /* @var $headingOptions array */
            $headingOptions = $configuration[HeadingOptions::FIELD];
            $this->configuration->set(HeadingOptions::CONFIG, $headingOptions);
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
        return isset($configuration[HeadingOptions::FIELD])
            && isset($configuration[SourceOptions::FIELD]);
    }

}
