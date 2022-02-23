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

namespace WEM\SmartgearBundle\Backend\Module\Core;

use Contao\FrontendTemplate;
use Contao\Input;
use Contao\RequestToken;
use Exception;
use InvalidArgumentException;
use WEM\SmartgearBundle\Classes\Backend\Dashboard as BackendDashboard;
use WEM\SmartgearBundle\Classes\Util;
use WEM\SmartgearBundle\Config\Core as CoreConfig;

class Dashboard extends BackendDashboard
{
    /** @var string */
    protected $strTemplate = 'be_wem_sg_block_core_dashboard';

    public function processAjaxRequest(): void
    {
        try {
            if (empty(Input::post('action'))) {
                throw new InvalidArgumentException('No action specified');
            }
            switch (Input::post('action')) {
                default:
                    parent::processAjaxRequest();
                break;
            }
        } catch (Exception $e) {
            $arrResponse = ['status' => 'error', 'msg' => $e->getMessage(), 'trace' => $e->getTrace()];
        }

        // Add Request Token to JSON answer and return
        $arrResponse['rt'] = RequestToken::get();
        echo json_encode($arrResponse);
        exit;
    }

    public function setDevMode(): void
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        $config->setSgMode(CoreConfig::MODE_DEV);
        $this->configurationManager->save($config);
    }

    public function setProdMode(): void
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();
        $config->setSgMode(CoreConfig::MODE_PROD);
        $this->configurationManager->save($config);
    }

    protected function getFilledTemplate(): FrontendTemplate
    {
        $objTemplate = parent::getFilledTemplate();
        /** @var CoreConfig */
        $config = $this->configurationManager->load();

        if (CoreConfig::MODE_DEV === $config->getSgMode()) {
            $this->actions[] = ['action' => 'prod_mode', 'label' => 'Mode production'];
        } else {
            $this->actions[] = ['action' => 'dev_mode', 'label' => 'Mode développement'];
        }

        $this->actions[] = ['action' => 'configure', 'label' => 'Configuration'];

        $objTemplate->actions = Util::formatActions($this->actions);

        return $objTemplate;
    }
}
