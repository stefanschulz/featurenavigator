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
