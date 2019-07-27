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

namespace WEM\SmartGear\Hooks;

use WEM\SmartGear\Backend\Updater;

/**
 * Class GetPageLayoutHook
 *
 * Handle Smartgear getPageLayout hooks
 */
class GetPageLayoutHook
{
    /**
     * Security Token
     *
     * @var string
     */
    private static $strSecurityToken = "SmartGear2o19";

    /**
     * Provide API Token to external websites
     *
     * @return string
     *
     * @todo find a better way to restrict this to specific domains
     */
    public function generateApiToken()
    {
        try {
            if ("getToken" == \Input::get('action') && self::$strSecurityToken == \Input::get('security')) {
                $container = \System::getContainer();
                $token = $container->get('contao.csrf.token_manager')->getToken($container->getParameter('contao.csrf_token_name'))->getValue();
                $arrResponse = ["status"=>"success", "token"=>$token];
            } else {
                throw new \Exception("Forbidden");
            }
        } catch (\Exception $e) {
            $arrResponse = ["status"=>"error", "msg"=>$e->getMessage()];
        }
        
        echo json_encode($arrResponse);
        die;
    }
}
