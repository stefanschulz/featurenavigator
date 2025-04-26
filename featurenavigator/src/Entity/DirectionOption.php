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

namespace PrestaShop\Module\FeatureNavigator\Entity;

if (!defined('_PS_VERSION_')) {
    exit;
}

class DirectionOption
{
    private string $label;
    private string $value;
    private string $order;

    public function __construct(string $label, string $value, string $order)
    {
        $this->label = $label;
        $this->value = $value;
        $this->order = $order;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getOrder(): string
    {
        return $this->order;
    }
}
