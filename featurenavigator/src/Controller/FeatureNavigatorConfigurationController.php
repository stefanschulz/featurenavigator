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

namespace PrestaShop\Module\FeatureNavigator\Controller;

use PrestaShop\Module\FeatureNavigator\Entity\Definitions;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

if (!defined('_PS_VERSION_')) {
    exit;
}

class FeatureNavigatorConfigurationController extends FrameworkBundleAdminController
{
    public function index(Request $request): Response
    {
        $formDataHandler = $this->get('prestashop.module.featurenavigator.form.form_data_handler');
        $form = $formDataHandler->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $errors = $formDataHandler->save($form->getData());
            if (empty($errors)) {
                $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('feature_navigator_configuration');
            }
            $this->flashErrors(array_map(function (string $message) {
                return $this->trans($message, Definitions::TRANS_ADMIN);
            }, $errors));
        }

        return $this->render('@Modules/featurenavigator/views/templates/admin/form.html.twig', [
            'configurationForm' => $form->createView(),
        ]);
    }
}
