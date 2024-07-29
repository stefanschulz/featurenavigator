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

use PrestaShop\Module\FeatureNavigator\Entity\Definitions;
use PrestaShop\Module\FeatureNavigator\Entity\HeadingOptions;
use PrestaShop\Module\FeatureNavigator\Entity\SourceOptions;

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
class FeatureNavigatorProductsModuleFrontController extends \ModuleFrontController
{
    /**
     * @throws \PrestaShopException
     */
    public function initContent(): void
    {
        parent::initContent();
        $heading = Configuration::get(HeadingOptions::CONFIG, $this->context->language->id);
        $feature = Configuration::get(SourceOptions::CONFIG, $this->context->language->id);
        $featureValue = Tools::getValue('feature', '');
        $entries = $featureValue ? $this->getProductsFilteredBy(urldecode($featureValue), $feature) : [];
        $this->context->smarty->assign(
            [
                'baseUrl' => 'featurenavigator',
                'heading' => $this->ensureHeading($heading),
                'feature' => $feature,
                'featureValue' => $featureValue,
                'entries' => $entries,
            ]
        );
        $this->setTemplate('module:featurenavigator/views/templates/front/products.tpl');
    }

    /**
     * Get products depending on the given feature filtered by a feature value.
     *
     * @param string $featureValue The feature value to filter by
     * @param string $feature The source to filter on
     *
     * @return array The list of products
     *
     * @throws PrestaShopDatabaseException
     */
    private function getProductsFilteredBy(string $featureValue, string $feature): array
    {
        $db = Db::getInstance(_PS_USE_SQL_SLAVE_);
        $sql = SourceOptions::getProductSql($featureValue, $feature, $this->context->language->id, $this->context->shop->id);
        if (empty($sql)) {
            return [];
        }
        $result = $db->executeS($sql, true, false);
        if (empty($result)) {
            return [];
        }
        $products = $result;

        // foreach ($result as $row) {
        //      $topic = $row['?'];
        //      $products[] = [
        //      ];
        // }

        return $products;
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
}
