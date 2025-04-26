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

use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;

if (!defined('_PS_VERSION_')) {
    exit;
}

class FeatureNavigatorFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var DataConfigurationInterface
     */
    private DataConfigurationInterface $dataConfiguration;

    /**
     * @param DataConfigurationInterface $dataConfiguration
     */
    public function __construct(DataConfigurationInterface $dataConfiguration)
    {
        $this->dataConfiguration = $dataConfiguration;
    }

    public function getData(): array
    {
        return $this->dataConfiguration->getConfiguration();
    }

    public function setData(array $data): array
    {
        return $this->dataConfiguration->updateConfiguration($data);
    }
}
