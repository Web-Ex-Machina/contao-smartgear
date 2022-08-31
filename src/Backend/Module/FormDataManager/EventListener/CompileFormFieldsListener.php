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

use Contao\Environment;
use Contao\Form;
use Contao\FormFieldModel;
use Contao\System;
use Exception;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as CoreConfigurationManager;
use WEM\SmartgearBundle\Classes\FormUtil;
use WEM\SmartgearBundle\Classes\Util;
use WEM\SmartgearBundle\Config\Component\Core\Core as CoreConfig;
use WEM\SmartgearBundle\Config\Module\FormDataManager\FormDataManager as FormDataManagerConfig;
use WEM\SmartgearBundle\Model\FormField;

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
                if ((bool) $form->getModel()->storeViaFormDataManager) {
                    // current page
                    $objPage = FormUtil::getPageFromForm($form);

                    $objFormFieldWarning = (new FormField());
                    $objFormFieldWarning->pid = $form->getModel()->id;
                    $objFormFieldWarning->sorting = 16;
                    $objFormFieldWarning->type = 'html';
                    $objFormFieldWarning->html = Util::getLocalizedTemplateContent('{root}/public/bundles/wemsmartgear/examples/formDataManager/{lang}/warning_message.html', 'FE' === TL_MODE ? $objPage->rootLanguage : \Contao\BackendUser::getInstance()->language, '{root}/public/bundles/wemsmartgear/examples/formDataManager/fr/warning_message.html');

                    // add this field at the beginning of the array
                    $arrFields = array_reverse($arrFields, true);
                    $arrFields['warning'] = $objFormFieldWarning;
                    $arrFields = array_reverse($arrFields, true);

                    $objFormFieldFirstAppearance = (new FormFieldModel());
                    $objFormFieldFirstAppearance->name = 'fdm[first_appearance]';
                    $objFormFieldFirstAppearance->type = 'hidden';
                    $arrFields['first_appearance'] = $objFormFieldFirstAppearance;

                    $objFormFieldFirstInteraction = (new FormFieldModel());
                    $objFormFieldFirstInteraction->name = 'fdm[first_interaction]';
                    $objFormFieldFirstInteraction->type = 'hidden';
                    $arrFields['first_interaction'] = $objFormFieldFirstInteraction;

                    $objFormFieldCurrentPage = (new FormFieldModel());
                    $objFormFieldCurrentPage->name = 'fdm[current_page]';
                    $objFormFieldCurrentPage->type = 'hidden';
                    $objFormFieldCurrentPage->value = $objPage ? $objPage->id : 0;
                    $arrFields['current_page'] = $objFormFieldCurrentPage;

                    $objFormFieldCurrentPage = (new FormFieldModel());
                    $objFormFieldCurrentPage->name = 'fdm[current_page_url]';
                    $objFormFieldCurrentPage->type = 'hidden';
                    $objFormFieldCurrentPage->value = Environment::get('uri');
                    $arrFields['current_page_url'] = $objFormFieldCurrentPage;

                    // Previous page
                    $objFormFieldRefererPage = (new FormFieldModel());
                    $objFormFieldRefererPage->name = 'fdm[referer_page_url]';
                    $objFormFieldRefererPage->type = 'hidden';
                    $objFormFieldRefererPage->value = System::getReferer();
                    $arrFields['referer_page_url'] = $objFormFieldRefererPage;
                }
            }
        } catch (Exception $e) {
            throw $e;
        }

        return $arrFields;
    }
}
