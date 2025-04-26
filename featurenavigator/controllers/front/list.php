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
use PrestaShop\Module\FeatureNavigator\Entity\DirectionOption;
use PrestaShop\Module\FeatureNavigator\Entity\DirectionOptions;
use PrestaShop\Module\FeatureNavigator\Entity\HeadingOptions;
use PrestaShop\Module\FeatureNavigator\Entity\SourceOptions;

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once __DIR__ . '/../../vendor/autoload.php';

/**
 * Front controller for listing feature items based on a given letter..
 *
 * @noinspection PhpIllegalPsrClassPathInspection
 * @noinspection PhpUnused
 */
class FeatureNavigatorListModuleFrontController extends \ModuleFrontController
{
    /**
     * @throws \PrestaShopException
     */
    public function initContent(): void
    {
        parent::initContent();
        $letter = Tools::getValue('letter', 'a');
        $heading = Configuration::get(HeadingOptions::CONFIG, $this->context->language->id);
        $source = Configuration::get(SourceOptions::CONFIG);
        $direction = DirectionOptions::getOrDefault(Configuration::get(DirectionOptions::CONFIG));
        $entries = $this->getEntriesFilteredBy($letter, $source, $direction);
        $this->context->smarty->assign(
            [
                'baseUrl' => 'featurenavigator',
                'heading' => $this->ensureHeading($heading),
                'letter' => SourceOptions::adjustValue($letter),
                'entries' => $entries,
                'source' => $source,
                'direction' => $direction->getLabel(),
            ]
        );
        $this->setTemplate('module:featurenavigator/views/templates/front/list.tpl');
    }

    /**
     * Get entries depending on the given source and filtered for the passed letter. Returning them in
     * the desired order direction.
     *
     * @param mixed $letter The letter for filtering
     * @param string $source The source to filter by
     * @param DirectionOption $direction The direction to order the entries in
     *
     * @return array The list of entries
     *
     * @throws PrestaShopDatabaseException
     */
    private function getEntriesFilteredBy(string $letter, string $source, DirectionOption $direction): array
    {
        $db = Db::getInstance(_PS_USE_SQL_SLAVE_);
        $sql = SourceOptions::getSql($letter, $source, $direction->getOrder(), $this->context->language->id, $this->context->shop->id);
        if (empty($sql)) {
            return [];
        }
        $result = $db->executeS($sql, true, false);
        if (empty($result)) {
            return [];
        }
        $entries = [];
        foreach ($result as $row) {
            $topic = $row['topic'];
            $entries[] = [
                'topic' => $topic,
                'param' => urlencode($topic),
            ];
        }

        return $entries;
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
