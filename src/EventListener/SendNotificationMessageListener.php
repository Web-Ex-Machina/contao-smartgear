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

namespace WEM\SmartgearBundle\EventListener;

use Contao\FormFieldModel;

class SendNotificationMessageListener
{

    public function __invoke($objMessage, &$arrTokens, $language, $objGatewayModel): bool
    {
        $arrTokens2 = [];

        foreach($arrTokens as $key=>$value){
            $chunks = explode('_',(string) $key);
            if($chunks === []){
                continue;
            }

            if('form' === $chunks[0]){
                unset($chunks[0]);
                $formFieldKey = implode('',$chunks);
                $arrTokens2[$formFieldKey]['value'] = $value;
            }elseif('formlabel' === $chunks[0]
            ){
                unset($chunks[0]);
                $formFieldKey = implode('',$chunks);
                $arrTokens2[$formFieldKey]['label'] = $value;
            }
        }

        foreach($arrTokens2 as $fieldName => $fieldDefinition){
            $objFormField = FormFieldModel::findOneBy(['pid = ?','name = ?'],[$arrTokens['formconfig_id'],$fieldName]);
            if(!$objFormField
            || $objFormField->is_technical_field 
            ){
                unset($arrTokens2[$fieldName]);
                continue;
            }

            $arrTokens['useful_data_arr'][$fieldDefinition['label']] = $fieldDefinition['value'];
            if(!empty($fieldDefinition['value'])){
                $arrTokens['useful_data_filled_arr'][$fieldDefinition['label']] = $fieldDefinition['value'];
            }
        }

        foreach($arrTokens['useful_data_arr'] as $label => $value){
            $arrTokens['useful_data'].=sprintf("%s: %s\n",$label,$value);
        }

        foreach($arrTokens['useful_data_filled_arr'] as $label => $value){
            $arrTokens['useful_data_filled'].=sprintf("%s: %s\n",$label,$value);
        }

        unset($arrTokens['useful_data_arr']);
        unset($arrTokens['useful_data_filled_arr']);

        return true;
    }
}
