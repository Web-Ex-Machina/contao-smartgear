<?php

declare(strict_types=1);

/**
 * SMARTGEAR for Contao Open Source CMS
 * Copyright (c) 2015-2023 Web ex Machina
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
use WEM\SmartgearBundle\Exceptions\File\NotFound;
use WEM\SmartgearBundle\Model\FormField;

class CompileFormFieldsListener
{
    public function __construct(protected CoreConfigurationManager $coreConfigurationManager)
    {
    }

    /**
     * @throws Exception
     */
    public function __invoke(
        array $arrFields,
        string $formId,
        Form $form
    ): array {
        try {
            /** @var CoreConfig $config */
            $coreConfig = $this->coreConfigurationManager->load();
            if ($coreConfig->getSgInstallComplete()) {
                // current page
                global $objPage;

                $objFormFieldWarning = (new FormField());
                $objFormFieldWarning->pid = $form->getModel()->id;
                $objFormFieldWarning->sorting = 16;
                $objFormFieldWarning->type = 'html';
                $objFormFieldWarning->html = '<div class="mt-2">'.Util::getLocalizedTemplateContent('{root}/templates/smartgear/settings/{lang}/form_warning_message.html5', 'FE' === TL_MODE ? $objPage->rootLanguage : \Contao\BackendUser::getInstance()->language, '{root}/templates/smartgear/settings/fr/form_warning_message.html5').'</div>';

                $arrFields['warning'] = $objFormFieldWarning;
            }
        } catch (NotFound) {
            return $arrFields;
        } catch (Exception $e) {
            throw $e;
        }

        return $arrFields;
    }
}
