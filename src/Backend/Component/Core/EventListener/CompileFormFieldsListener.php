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

namespace WEM\SmartgearBundle\Backend\Component\Core\EventListener;

use Contao\Form;
use Exception;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as CoreConfigurationManager;
use WEM\SmartgearBundle\Classes\Util;
use WEM\SmartgearBundle\Config\Component\Core\Core as CoreConfig;
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
            if ($coreConfig->getSgInstallComplete()) {
                // current page
                global $objPage;

                $objFormFieldWarning = (new FormField());
                $objFormFieldWarning->pid = $form->getModel()->id;
                $objFormFieldWarning->sorting = 16;
                $objFormFieldWarning->type = 'html';
                $objFormFieldWarning->html = Util::getLocalizedTemplateContent('{root}/templates/smartgear/settings/{lang}/form_warning_message.html5', 'FE' === TL_MODE ? $objPage->rootLanguage : \Contao\BackendUser::getInstance()->language, '{root}/templates/smartgear/settings/fr/form_warning_message.html5');

                $arrFields['warning'] = $objFormFieldWarning;
            }
        } catch (Exception $e) {
            throw $e;
        }

        return $arrFields;
    }
}
