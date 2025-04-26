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

final class DirectionOptions
{
    const CONFIG = 'FEATURE_NAVIGATOR_DIRECTION';
    const FIELD = 'direction';
    const FIELD_LABEL = 'Direction';
    /**
     * @var DirectionOption[]
     */
    private static array $OPTIONS = [];

    /**
     * @return DirectionOption[]
     */
    private static function getOptionDefinitions(): array
    {
        if (empty(self::$OPTIONS)) {
            self::$OPTIONS = [
                'ascending' => new DirectionOption('Ascending', 'ascending', 'ASC'),
                'descending' => new DirectionOption('Descending', 'descending', 'DESC'),
            ];
        }

        return self::$OPTIONS;
    }

    /**
     * @return DirectionOption[]
     */
    public static function getOptions(): array
    {
        return array_values(self::getOptionDefinitions());
    }

    public static function getDefault(): DirectionOption
    {
        return self::ascending();
    }

    public static function getOrDefault($direction): DirectionOption
    {
        $options = self::getOptionDefinitions();
        if (isset($options[$direction])) {
            return $options[$direction];
        }

        return self::getDefault();
    }

    public static function ascending(): DirectionOption
    {
        return self::getOptionDefinitions()['ascending'];
    }

    public static function descending(): DirectionOption
    {
        return self::getOptionDefinitions()['descending'];
    }
}
