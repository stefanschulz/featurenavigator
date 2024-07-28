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

namespace PrestaShop\Module\FeatureNavigator\Form;

if (!defined('_PS_VERSION_')) {
    exit;
}

use PrestaShop\Module\FeatureNavigator\Entity\Definitions;
use PrestaShop\Module\FeatureNavigator\Entity\DirectionOption;
use PrestaShop\Module\FeatureNavigator\Entity\DirectionOptions;
use PrestaShop\Module\FeatureNavigator\Entity\SourceOptions;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;

class FeatureNavigatorConfigurationFormType extends TranslatorAwareType
{
    private FormChoiceProviderInterface $featureChoiceProvider;

    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        FormChoiceProviderInterface $featuresChoiceProvider,
    ) {
        parent::__construct($translator, $locales);
        $this->featureChoiceProvider = $featuresChoiceProvider;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $featureChoices = $this->featureChoiceProvider->getChoices();
        $builder
            ->add(SourceOptions::FIELD, ChoiceType::class, [
                'label' => $this->trans(SourceOptions::FIELD_LABEL, Definitions::TRANS_ADMIN),
                'multiple' => false,
                'choices' => $featureChoices,
            ])
            ->add(DirectionOptions::FIELD, ChoiceType::class, [
                'label' => $this->trans(DirectionOptions::FIELD_LABEL, Definitions::TRANS_ADMIN),
                'multiple' => false,
                'choices' => DirectionOptions::getOptions(),
                'choice_label' => function (DirectionOption $option): string {
                    return $this->trans($option->getLabel(), Definitions::TRANS_ADMIN);
                },
                'choice_value' => 'value',
                'expanded' => true,
            ]);
    }
}
