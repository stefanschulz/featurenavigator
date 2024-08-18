<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */
/*
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

use PrestaShop\Module\FeatureNavigator\Entity\Definitions;
use PrestaShop\Module\FeatureNavigator\Entity\HeadingOptions;
use PrestaShop\Module\FeatureNavigator\Entity\SourceOptions;
use PrestaShop\Module\FeatureNavigator\Search\FeatureNavigatorProductSearchProvider;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once __DIR__ . '/../../vendor/autoload.php';

/**
 * Front controller for listing products of a selected feature item.
 *
 * @noinspection PhpIllegalPsrClassPathInspection
 * @noinspection PhpUnused
 */
class FeatureNavigatorProductsModuleFrontController extends ProductListingFrontController
{
    private Module $module;
    private string|false $feature;
    private string|false $heading;

    public function __construct()
    {
        $this->module = Module::getInstanceByName(Definitions::MODULE_NAME);
        parent::__construct();
        $this->controller_type = 'modulefront';
    }

    public function initContent(): void
    {
        parent::initContent();

        $this->heading = Configuration::get(HeadingOptions::CONFIG, $this->context->language->id);
        $this->feature = Configuration::get(SourceOptions::CONFIG, $this->context->language->id);
        $featureValue = $this->validate(Tools::getValue('feature', ''));

        if (!$featureValue) {
            Tools::redirect('feature-navigator-list');
        }

        $this->context->smarty->assign(
            [
                'baseUrl' => 'featurenavigator',
                'heading' => $this->ensureHeading($this->heading),
                'feature' => $this->feature,
                'featureValue' => urldecode($featureValue),
            ]
        );
        $template = '../../../modules/featurenavigator/views/templates/front/products.tpl';
        $this->doProductSearch($template);
    }

    protected function getAjaxProductSearchVariables(): array
    {
        $search = $this->getProductSearchVariables();

        $rendered_products_top = $this->render('module:featurenavigator/views/templates/front/_partials/products_top', ['listing' => $search]);
        $rendered_products = $this->render('catalog/_partials/products', ['listing' => $search]);
        $data = array_merge(
            [
                'rendered_products_top' => $rendered_products_top,
                'rendered_products' => $rendered_products,
            ],
            $search
        );

        if (!empty($data['products']) && is_array($data['products'])) {
            $data['products'] = $this->prepareProductArrayForAjaxReturn($data['products']);
        }

        return $data;
    }

    public function getTemplateFile($template, $params = [], $locale = null)
    {
        if (str_starts_with($template, 'module:')) {
            return str_replace('module:', _PS_ROOT_DIR_ . _MODULE_DIR_, $template) . '.tpl';
        }

        return parent::getTemplateFile($template, $params, $locale);
    }

    public function getListingLabel(): bool|string
    {
        return $this->ensureHeading($this->heading);
    }

    protected function getProductSearchQuery(): ProductSearchQuery
    {
        $query = new ProductSearchQuery();
        $query->setQueryType('feature-search');
        $featureValue = Tools::getValue('feature', '');
        $query->setSearchString(urldecode($featureValue));
        $query->setSearchTag($this->feature);

        return $query;
    }

    protected function getDefaultProductSearchProvider(): FeatureNavigatorProductSearchProvider
    {
        return new FeatureNavigatorProductSearchProvider(
            Db::getInstance(_PS_USE_SQL_SLAVE_)
        );
    }

    public function setMedia(): void
    {
        parent::setMedia();

        $this->registerStylesheet(
            'module-' . $this->module->name . '-style',
            'modules/' . $this->module->name . '/public/css/' . $this->module->name . '.css',
            [
                'media' => 'all',
                'priority' => 200,
                'version' => 'release-2024-07',
            ]
        );
    }

    private function ensureHeading(false|string $heading): string
    {
        return $heading ?: $this->getTranslator()->trans('Heading not translated', [], Definitions::TRANS_ADMIN);
    }

    public function getBreadcrumbLinks(): array
    {
        $breadcrumb = parent::getBreadcrumbLinks();

        $breadcrumb['links'][] = [
            'title' => Configuration::get(HeadingOptions::CONFIG, $this->context->language->id),
            'url' => $this->context->link->getModuleLink('featurenavigator', 'products'),
        ];

        return $breadcrumb;
    }

    private function validate(mixed $featureValue): false|string
    {
        if (empty($featureValue) || !is_string($featureValue)) {
            return false;
        }
        $db = Db::getInstance(_PS_USE_SQL_SLAVE_);
        $sql = SourceOptions::getAllSql($this->feature, $this->context->language->id, $this->context->shop->id);
        if (empty($sql)) {
            return false;
        }
        try {
            $topic = urldecode($featureValue);
            $result = $db->executeS($sql, true, false);
            foreach ($result as $row) {
                if ($row['topic'] == $topic) {
                    return $topic;
                }
            }
        } catch (PrestaShopDatabaseException $e) {
            // ignore
        }

        return false;
    }
}
