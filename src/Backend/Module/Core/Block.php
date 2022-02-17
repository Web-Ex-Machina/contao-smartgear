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

use Contao\Input;
use Contao\System;
use Exception;
use WEM\SmartgearBundle\Classes\Backend\Block as BackendBlock;

class Block extends BackendBlock
{
    protected $type = 'core';
    protected $module = 'core';
    protected $icon = 'exclamation-triangle';
    protected $title = 'Core';

    public function processAjaxRequest()
    {
        try {
            switch (Input::post('action')) {
                case 'framwayRetrieval':
                    $framwayRetrievalStep = System::getContainer()->get('smartgear.backend.module.core.configuration_step.framway_retrieval');
                    $arrResponse['status'] = 'success';
                    $arrResponse['msg'] = 'Le framway a été récupéré avec succès';
                    $res = $framwayRetrievalStep->retrieve();
                    $arrResponse['output'] = $res;
                break;
                default:
                    return parent::processAjaxRequest();
                break;
            }
        } catch (Exception $e) {
            $arrResponse = ['status' => 'error', 'msg' => $e->getMessage(), 'trace' => $e->getTrace()];
        }

        return $arrResponse;
    }
}
