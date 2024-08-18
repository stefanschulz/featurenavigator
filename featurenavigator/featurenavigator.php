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

require_once __DIR__ . '/vendor/autoload.php';

use PrestaShop\Module\FeatureNavigator\Entity\Definitions;
use PrestaShop\Module\FeatureNavigator\Entity\DirectionOptions;
use PrestaShop\Module\FeatureNavigator\Entity\HeadingOptions;
use PrestaShop\Module\FeatureNavigator\Entity\SourceOptions;

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Class FeatureNavigator
 *
 * @noinspection PhpIllegalPsrClassPathInspection
 */
class FeatureNavigator extends Module
{
    public function __construct()
    {
        $this->name = Definitions::MODULE_NAME;
        $this->author = 'Stefan Schulz';
        $this->version = '0.1.0';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = [
            'min' => '8.0.0',
            'max' => '8.99.99',
        ];
        $this->tab = 'front_office_features';

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->trans('Feature Navigator', [], Definitions::TRANS_ADMIN);
        $this->description = $this->trans('Navigate based on a feature.', [], Definitions::TRANS_ADMIN);
    }

    public function install(): bool
    {
        return parent::install()
            && $this->installConfigs()
            && $this->installHooks();
    }

    public function uninstall(): bool
    {
        return $this->uninstallConfigs()
            && $this->uninstallHooks()
            && parent::uninstall();
    }

    private function installConfigs(): bool
    {
        return Configuration::updateValue(SourceOptions::CONFIG, SourceOptions::getDefault())
            && Configuration::updateValue(DirectionOptions::CONFIG, DirectionOptions::getDefault()->getValue())
            && Configuration::updateValue(HeadingOptions::CONFIG, []);
    }

    private function uninstallConfigs(): bool
    {
        return Configuration::deleteByName(SourceOptions::CONFIG)
            && Configuration::deleteByName(DirectionOptions::CONFIG)
            && Configuration::deleteByName(HeadingOptions::CONFIG);
    }

    private function installHooks(): bool
    {
        return $this->registerHook('moduleRoutes')
            && $this->registerHook('displayBackOfficeHeader');
    }

    private function uninstallHooks(): bool
    {
        return $this->unregisterHook('moduleRoutes')
            && $this->unregisterHook('displayBackOfficeHeader');
    }

    public function isUsingNewTranslationSystem(): bool
    {
        return true;
    }

    /**
     * Backoffice admin content provisioning.
     *
     * @return void
     *
     * @throws Exception
     */
    public function getContent(): void
    {
        $route = $this->get('router')->generate('feature_navigator_configuration');
        Tools::redirectAdmin($route);
    }

    /**
     * Function hooked for routing the front controller.
     *
     * @noinspection PhpUnused
     */
    public function hookModuleRoutes(): array
    {
        return [
            'feature-navigator-list' => [
                'rule' => 'featurenavigator/list',
                'keywords' => [],
                'controller' => 'list',
                'params' => [
                    'fc' => 'module',
                    'module' => 'featurenavigator',
                ],
            ],
            'feature-navigator-list-arg' => [
                'rule' => 'featurenavigator/list/{letter}',
                'keywords' => [
                    'letter' => [
                        'regexp' => '[\w]?',
                        'param' => 'letter',
                    ],
                ],
                'controller' => 'list',
                'params' => [
                    'fc' => 'module',
                    'module' => 'featurenavigator',
                ],
            ],
            'feature-navigator-list-products' => [
                'rule' => 'featurenavigator/products/{feature}',
                'keywords' => [
                    'feature' => [
                        'regexp' => '.*', // '[\w\S]*',
                        'param' => 'feature',
                    ],
                ],
                'controller' => 'products',
                'params' => [
                    'fc' => 'module',
                    'module' => 'featurenavigator',
                ],
            ],
        ];
    }

    /**
     * Function hooked for enhancing the back office form.
     *
     * @noinspection PhpUnused
     */
    public function hookDisplayBackOfficeHeader()
    {
        //        return '<script type="module" src="' . $this->getPathUri() . 'views/js/form.js">';
        $this->context->controller->addJS($this->getPathUri() . 'views/js/form.js');
    }
}
