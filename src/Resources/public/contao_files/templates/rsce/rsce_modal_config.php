<?php

/**
 * rsce_modal_config.php
 * https://demo.smartgear.webexmachina.fr/guidelines.html
 */
return [
    'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_modal'], 
    'contentCategory' => 'links',
    'standardFields' => array('cssID'),
    'fields' => [
        'modal_legend' => array(
            'label' => [&$GLOBALS['TL_LANG']['tl_content']['rsce_modal']['modal_legend']], 
            'inputType' => 'group',
        ),
        'modal_title' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_modal']['modal_title'], 
            'inputType' => 'text',
            'eval' => array('tl_class'=>'w50')
        ),
        'content_legend' => array(
            'label' => [&$GLOBALS['TL_LANG']['tl_content']['content_legend']],
            'inputType' => 'group',
        ),
        'content_type' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_modal']['content_type'], 
            'inputType' => 'select',
            'options' => array(
                'text'    => &$GLOBALS['TL_LANG']['tl_content']['rsce_modal']['content_type']['text'],
                'picture' => &$GLOBALS['TL_LANG']['tl_content']['rsce_modal']['content_type']['picture'],
                'article' => &$GLOBALS['TL_LANG']['tl_content']['rsce_modal']['content_type']['article'],
                'form'    => &$GLOBALS['TL_LANG']['tl_content']['rsce_modal']['content_type']['form'],
                'module'  => &$GLOBALS['TL_LANG']['tl_content']['rsce_modal']['content_type']['module'],
                'html'    => &$GLOBALS['TL_LANG']['tl_content']['rsce_modal']['content_type']['html'],
            ),
            'eval' => array('tl_class'=>'w50'),
        ),
        'text' => array(
            'inputType' => 'standardField',
            'eval' => array('mandatory'=>false, 'tl_class'=>'clr'),
            'dependsOn' => array(
                'field' => 'content_type', 
                'value' => 'text',      
            ),
        ),
        'html' => array(
            'inputType' => 'standardField',
            'eval' => array('mandatory'=>false, 'tl_class'=>'clr'),
            'dependsOn' => array(
                'field' => 'content_type', 
                'value' => 'html',      
            ),
        ),
        'article' => array(
            'inputType' => 'standardField',
            'eval' => array('mandatory'=>false, 'tl_class'=>'clr'),
            'dependsOn' => array(
                'field' => 'content_type', 
                'value' => 'article',      
            ),
        ),
        'form' => array(
            'inputType' => 'standardField',
            'eval' => array('mandatory'=>false, 'tl_class'=>'clr'),
            'dependsOn' => array(
                'field' => 'content_type', 
                'value' => 'form',      
            ),
        ),
        'module' => array(
            'inputType' => 'standardField',
            'eval' => array('mandatory'=>false, 'tl_class'=>'clr'),
            'dependsOn' => array(
                'field' => 'content_type', 
                'value' => 'module',      
            ),
        ),
        'singleSRC' => array(
            'inputType' => 'standardField',
            'eval' => array('mandatory'=>false,'extensions'=>Config::get('validImageTypes'), 'tl_class'=>'clr'),
            'dependsOn' => array(
                'field' => 'content_type', 
                'value' => 'picture',      
            ),
        ),
        'size' => array(
            'inputType' => 'standardField',
            'eval' => array('mandatory'=>false,'includeBlankOption'=>true, 'nospace'=>true, 'tl_class'=>'w50 clr'),
            'dependsOn' => array(
                'field' => 'content_type', 
                'value' => 'picture',      
            ),
        ),
        'alt' => array(
            'inputType' => 'standardField',
            'eval' => array('mandatory'=>false, 'tl_class'=>'w50 clr'),
            'dependsOn' => array(
                'field' => 'content_type', 
                'value' => 'picture',      
            ),
        ),
        'imageTitle' => array(
            'inputType' => 'standardField',
            'eval' => array('mandatory'=>false, 'tl_class'=>'w50'),
            'dependsOn' => array(
                'field' => 'content_type', 
                'value' => 'picture',      
            ),
        ),
        'trigger_legend' => array(
            'label' => [&$GLOBALS['TL_LANG']['tl_content']['rsce_modal']['trigger_legend']], 
            'inputType' => 'group',
        ),
        'trigger_type' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_modal']['trigger_type'], 
            'inputType' => 'select',
            'options' => array(
                'button' => &$GLOBALS['TL_LANG']['tl_content']['rsce_modal']['trigger_type']['button'], 
                'link'   => &$GLOBALS['TL_LANG']['tl_content']['rsce_modal']['trigger_type']['link'], 
                'onload' => &$GLOBALS['TL_LANG']['tl_content']['rsce_modal']['trigger_type']['onload'], 
                'custom' => &$GLOBALS['TL_LANG']['tl_content']['rsce_modal']['trigger_type']['custom'], 
            ),
            'eval' => array('tl_class'=>'w50'),
        ),
        'linkTitle' => array(
            'inputType' => 'standardField',
            'eval' => array('mandatory'=>false, 'tl_class'=>'clr w50'),
            'dependsOn' => array(
                'field' => 'trigger_type', 
                'value' => array('button','link'),      
            ),
        ),
        'titleText' => array(
            'inputType' => 'standardField',
            'eval' => array('mandatory'=>false, 'tl_class'=>' w50'),
            'dependsOn' => array(
                'field' => 'trigger_type', 
                'value' => array('button','link'),      
            ),
        ),
        'trigger_css' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_modal']['trigger_css'], 
            'inputType' => 'text',
            'eval' => array('mandatory'=>false, 'tl_class'=>' w50'),
            'dependsOn' => array(
                'field' => 'trigger_type', 
                'value' => array('button','link'),      
            ),
        ),
        'trigger_custom' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_modal']['trigger_custom'], 
            'inputType' => 'text',
            'eval' => array('mandatory'=>false, 'class'=>'monospace', 'rte'=>'ace|html','tl_class'=>'clr'),
            'dependsOn' => array(
                'field' => 'trigger_type', 
                'value' => 'custom',      
            ),
        ),
        'advanced_legend' => array(
            'label' => [&$GLOBALS['TL_LANG']['tl_content']['advanced_legend']],
            'inputType' => 'group',
        ),
        'modal_name' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_modal']['modal_name'], 
            'inputType' => 'text',
            'eval' => array('tl_class'=>'w50'),
        ),
        'modal_autoload' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_modal']['modal_autoload'], 
            'inputType' => 'checkbox',
            'eval' => array( 'tl_class' => 'w50 clr'),
            'default' => true
        ),
        'modal_autodestroy' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_modal']['modal_autodestroy'], 
            'inputType' => 'checkbox',
            'eval' => array( 'tl_class' => 'w50 clr'),
        ),
        'modal_refresh' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_modal']['modal_refresh'], 
            'inputType' => 'checkbox',
            'eval' => array( 'tl_class' => 'w50 clr'),
        ),
    ]
];
