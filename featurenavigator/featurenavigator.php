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
        $this->version = '1.0.1';
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
