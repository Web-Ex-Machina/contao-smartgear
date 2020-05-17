<?php

/**
 * SMARTGEAR for Contao Open Source CMS
 *
 * Copyright (c) 2015-2019 Web ex Machina
 *
 * @category ContaoBundle
 * @package  Web-Ex-Machina/contao-smartgear
 * @author   Web ex Machina <contact@webexmachina.fr>
 * @link     https://github.com/Web-Ex-Machina/contao-smartgear/
 */

namespace WEM\SmartgearBundle\Hooks;

use WEM\SmartgearBundle\Backend\Updater;

/**
 * Class ExecutePreActionsHook
 *
 * Handle Smartgear executePreActions hooks
 */
class ExecutePreActionsHook
{
    /**
     * Catch requests from external websites
     *
     * @return mixed|string
     */
    public function catchApiRequests()
    {
        try {
            if (\Input::post('SG_API') && \Input::post('SG_ACTION')) {
                switch (\Input::post('SG_ACTION')) {
                    case 'getSmartgearVersion':
                        $objUpdater = new Updater(false);
                        $arrResponse = ["status"=>"success", "version"=>$objUpdater->getCurrentVersion()];
                        break;

                    default:
                        throw new \Exception(sprintf("Unknown request called : %s", \Input::post('SG_ACTION')));
                }
            }
        } catch (\Exception $e) {
            $arrResponse = ["status"=>"error", "msg"=>$e->getMessage(), "trace"=>$e->getTrace()];
        }
        
        // Add Request Token to JSON answer and return
        $arrResponse["rt"] = \RequestToken::get();
        echo json_encode($arrResponse);
        die;
    }
}
