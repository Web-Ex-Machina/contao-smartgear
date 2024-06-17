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

namespace WEM\SmartgearBundle\Backend\Module\Extranet;

use Contao\FrontendTemplate;
use Contao\Input;
use Contao\RequestToken;
use Exception;
use InvalidArgumentException;
use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\SmartgearBundle\Classes\Backend\Dashboard as BackendDashboard;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as ConfigurationManager;
use WEM\SmartgearBundle\Config\Component\Core\Core as CoreConfig;

class Dashboard extends BackendDashboard
{
    /** @var string */
    protected string $strTemplate = 'be_wem_sg_block_extranet_dashboard';

    public function __construct(
        ConfigurationManager $configurationManager,
        TranslatorInterface $translator,
        string $module,
        string $type
    ) {
        parent::__construct($configurationManager, $translator, $module, $type);
    }

    public function processAjaxRequest(): void
    {
        try {
            if (empty(Input::post('action'))) {
                throw new InvalidArgumentException($GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['AjaxNoActionSpecified']);
            }

            parent::processAjaxRequest();
        } catch (Exception $exception) {
            $arrResponse = ['status' => 'error', 'msg' => $exception->getMessage(), 'trace' => $exception->getTrace()];
        }

        // Add Request Token to JSON answer and return
        $arrResponse['rt'] = RequestToken::get(); // TODO : deprecated Token
        echo json_encode($arrResponse);
        exit;
    }

    protected function getFilledTemplate(): FrontendTemplate
    {
        $objTemplate = parent::getFilledTemplate();
        /** @var CoreConfig $config */
        $config = $this->configurationManager->load();

        $objTemplate->installComplete = $config->getSgExtranet()->getSgInstallComplete();
        $objTemplate->installLocked = $config->getSgInstallLocked();

        if (!$config->getSgInstallLocked()) {
            if (!$config->getSgExtranet()->getSgInstallComplete()) {
                $this->actions[] = ['action' => 'install', 'label' => $GLOBALS['TL_LANG']['WEMSG']['CORE']['DASHBOARD']['buttonInstallationLabel']];
            } else {
                $this->actions[] = ['action' => 'configure', 'label' => $GLOBALS['TL_LANG']['WEMSG']['CORE']['DASHBOARD']['buttonConfigurationLabel']];
            }

            $this->actions[] = ['action' => 'reset_mode', 'label' => $GLOBALS['TL_LANG']['WEMSG']['CORE']['DASHBOARD']['buttonResetLabel']];
        }

        if ($config->getSgInstallLocked()) {
            $objTemplate->messages = array_merge($objTemplate->messages ?? [], [
                ['class' => 'tl_info', 'text' => $GLOBALS['TL_LANG']['WEMSG']['CORE']['DASHBOARD']['installLocked']],
            ]);
        }

        return $objTemplate;
    }
}
