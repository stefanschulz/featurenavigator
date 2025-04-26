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
