<?php

declare(strict_types=1);

/**
 * SMARTGEAR for Contao Open Source CMS
 * Copyright (c) 2015-2022 Web ex Machina
 *
 * @category ContaoBundle
 * @package  Web-Ex-Machina/contao-smartgear
 * @author   Web ex Machina <contact@webexmachina.fr>
 * @link     https://github.com/Web-Ex-Machina/contao-smartgear/
 */

namespace WEM\SmartgearBundle\Backend\Module\FormDataManager\EventListener;

use Contao\Form;
use Contao\FormFieldModel;
use Exception;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as CoreConfigurationManager;
use WEM\SmartgearBundle\Config\Component\Core\Core as CoreConfig;
use WEM\SmartgearBundle\Config\Module\FormDataManager\FormDataManager as FormDataManagerConfig;

class CompileFormFieldsListener
{
    /** @var CoreConfigurationManager */
    protected $coreConfigurationManager;

    public function __construct(
        CoreConfigurationManager $coreConfigurationManager
    ) {
        $this->coreConfigurationManager = $coreConfigurationManager;
    }

    public function __invoke(
        array $arrFields,
        string $formId,
        Form $form
    ): array {
        try {
            /** @var CoreConfig */
            $coreConfig = $this->coreConfigurationManager->load();
            /** @var FormDataManagerConfig */
            $fdmConfig = $coreConfig->getSgFormDataManager();
            if ($coreConfig->getSgInstallComplete()
            && $fdmConfig->getSgInstallComplete()
            ) {
                $objFormFieldFirstAppearance = (new FormFieldModel());
                $objFormFieldFirstAppearance->name = 'fdm[first_appearance]';
                $objFormFieldFirstAppearance->type = 'hidden';
                $arrFields['first_appearance'] = $objFormFieldFirstAppearance;

                $objFormFieldFirstInteraction = (new FormFieldModel());
                $objFormFieldFirstInteraction->name = 'fdm[first_interaction]';
                $objFormFieldFirstInteraction->type = 'hidden';
                $arrFields['first_interaction'] = $objFormFieldFirstInteraction;

                return $arrFields;
            }
        } catch (Exception $e) {
            throw $e;
        }
    }
}
