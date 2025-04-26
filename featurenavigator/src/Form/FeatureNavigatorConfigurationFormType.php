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

namespace PrestaShop\Module\FeatureNavigator\Form;

if (!defined('_PS_VERSION_')) {
    exit;
}

use PrestaShop\Module\FeatureNavigator\Entity\Definitions;
use PrestaShop\Module\FeatureNavigator\Entity\DirectionOption;
use PrestaShop\Module\FeatureNavigator\Entity\DirectionOptions;
use PrestaShop\Module\FeatureNavigator\Entity\HeadingOptions;
use PrestaShop\Module\FeatureNavigator\Entity\SourceOptions;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\DefaultLanguage;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;

class FeatureNavigatorConfigurationFormType extends TranslatorAwareType
{
    public const MAX_TITLE_LENGTH = 255;

    private FormChoiceProviderInterface $featureChoiceProvider;

    public function __construct(
        TranslatorInterface         $translator,
        array                       $locales,
        FormChoiceProviderInterface $featuresChoiceProvider,
    )
    {
        parent::__construct($translator, $locales);
        $this->featureChoiceProvider = $featuresChoiceProvider;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $featureChoices = $this->featureChoiceProvider->getChoices();
        $builder
            ->add(HeadingOptions::FIELD, TranslatableType::class, [
                'label' => $this->trans(HeadingOptions::FIELD_LABEL, Definitions::TRANS_ADMIN),
                'constraints' => [
                    new DefaultLanguage(),
                ],
                'options' => [
                    'constraints' => [
                        new Regex([
                                'pattern' => '/^[^<>={}]*$/u',
                                'message' => $this->trans(
                                    '%s is invalid.',
                                    'Admin.Notifications.Error'
                                ),
                            ]
                        ),
                        new Length([
                            'max' => static::MAX_TITLE_LENGTH,
                            'maxMessage' => $this->trans(
                                'This field cannot be longer than %limit% characters',
                                'Admin.Notifications.Error',
                                ['%limit%' => static::MAX_TITLE_LENGTH]
                            ),
                        ]),
                    ],
                ],
            ])
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
