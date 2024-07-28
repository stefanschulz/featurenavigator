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
