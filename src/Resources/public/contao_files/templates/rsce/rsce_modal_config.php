<?php

/**
 * rsce_modal_config.php
 * https://demo.smartgear.webexmachina.fr/guidelines.html
 */
return [
    'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_modal'], 
    'contentCategory' => 'links',
    'standardFields' => ['cssID'],
    'fields' => [
        'modal_legend' => [
            'label' => [&$GLOBALS['TL_LANG']['tl_content']['rsce_modal']['modal_legend']],
            'inputType' => 'group',
        ],
        'modal_title' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_modal']['modal_title'], 
            'inputType' => 'text',
            'eval' => ['tl_class'=>'w50']
        ],
        'content_legend' => [
            'label' => [&$GLOBALS['TL_LANG']['tl_content']['content_legend']],
            'inputType' => 'group',
        ],
        'content_type' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_modal']['content_type'], 
            'inputType' => 'select',
            'options' => [
                'text'    => &$GLOBALS['TL_LANG']['tl_content']['rsce_modal']['content_type']['text'],
                'picture' => &$GLOBALS['TL_LANG']['tl_content']['rsce_modal']['content_type']['picture'],
                'article' => &$GLOBALS['TL_LANG']['tl_content']['rsce_modal']['content_type']['article'],
                'form'    => &$GLOBALS['TL_LANG']['tl_content']['rsce_modal']['content_type']['form'],
                'module'  => &$GLOBALS['TL_LANG']['tl_content']['rsce_modal']['content_type']['module'],
                'html'    => &$GLOBALS['TL_LANG']['tl_content']['rsce_modal']['content_type']['html'],
            ],
            'eval' => ['tl_class'=>'w50'],
        ],
        'text' => [
            'inputType' => 'standardField',
            'eval' => ['mandatory'=>false, 'tl_class'=>'clr'],
            'dependsOn' => [
                'field' => 'content_type', 
                'value' => 'text',
            ],
        ],
        'html' => [
            'inputType' => 'standardField',
            'eval' => ['mandatory'=>false, 'tl_class'=>'clr'],
            'dependsOn' => [
                'field' => 'content_type', 
                'value' => 'html',
            ],
        ],
        'article' => [
            'inputType' => 'standardField',
            'eval' => ['mandatory'=>false, 'tl_class'=>'clr'],
            'dependsOn' => [
                'field' => 'content_type', 
                'value' => 'article',
            ],
        ],
        'form' => [
            'inputType' => 'standardField',
            'eval' => ['mandatory'=>false, 'tl_class'=>'clr'],
            'dependsOn' => [
                'field' => 'content_type', 
                'value' => 'form',
            ],
        ],
        'module' => [
            'inputType' => 'standardField',
            'eval' => ['mandatory'=>false, 'tl_class'=>'clr'],
            'dependsOn' => [
                'field' => 'content_type', 
                'value' => 'module',
            ],
        ],
        'singleSRC' => [
            'inputType' => 'standardField',
            'eval' => ['mandatory'=>false,'extensions'=>\Contao\Config::get('validImageTypes'), 'tl_class'=>'clr'],
            'dependsOn' => [
                'field' => 'content_type', 
                'value' => 'picture',
            ],
        ],
        'size' => [
            'inputType' => 'standardField',
            'eval' => ['mandatory'=>false,'includeBlankOption'=>true, 'nospace'=>true, 'tl_class'=>'w50 clr'],
            'dependsOn' => [
                'field' => 'content_type', 
                'value' => 'picture',
            ],
        ],
        'alt' => [
            'inputType' => 'standardField',
            'eval' => ['mandatory'=>false, 'tl_class'=>'w50 clr'],
            'dependsOn' => [
                'field' => 'content_type', 
                'value' => 'picture',
            ],
        ],
        'imageTitle' => [
            'inputType' => 'standardField',
            'eval' => ['mandatory'=>false, 'tl_class'=>'w50'],
            'dependsOn' => [
                'field' => 'content_type', 
                'value' => 'picture',
            ],
        ],
        'trigger_legend' => [
            'label' => [&$GLOBALS['TL_LANG']['tl_content']['rsce_modal']['trigger_legend']],
            'inputType' => 'group',
        ],
        'trigger_type' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_modal']['trigger_type'], 
            'inputType' => 'select',
            'options' => [
                'button' => &$GLOBALS['TL_LANG']['tl_content']['rsce_modal']['trigger_type']['button'], 
                'link'   => &$GLOBALS['TL_LANG']['tl_content']['rsce_modal']['trigger_type']['link'], 
                'onload' => &$GLOBALS['TL_LANG']['tl_content']['rsce_modal']['trigger_type']['onload'], 
                'custom' => &$GLOBALS['TL_LANG']['tl_content']['rsce_modal']['trigger_type']['custom'],
            ],
            'eval' => ['tl_class'=>'w50'],
        ],
        'linkTitle' => [
            'inputType' => 'standardField',
            'eval' => ['mandatory'=>false, 'tl_class'=>'clr w50'],
            'dependsOn' => [
                'field' => 'trigger_type', 
                'value' => ['button','link'],
            ],
        ],
        'titleText' => [
            'inputType' => 'standardField',
            'eval' => ['mandatory'=>false, 'tl_class'=>' w50'],
            'dependsOn' => [
                'field' => 'trigger_type', 
                'value' => ['button','link'],
            ],
        ],
        'trigger_css' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_modal']['trigger_css'], 
            'inputType' => 'text',
            'eval' => ['mandatory'=>false, 'tl_class'=>' w50'],
            'dependsOn' => [
                'field' => 'trigger_type', 
                'value' => ['button','link'],
            ],
        ],
        'trigger_custom' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_modal']['trigger_custom'], 
            'inputType' => 'text',
            'eval' => ['mandatory'=>false, 'class'=>'monospace', 'rte'=>'ace|html','tl_class'=>'clr'],
            'dependsOn' => [
                'field' => 'trigger_type', 
                'value' => 'custom',
            ],
        ],
        'advanced_legend' => [
            'label' => [&$GLOBALS['TL_LANG']['tl_content']['advanced_legend']],
            'inputType' => 'group',
        ],
        'modal_name' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_modal']['modal_name'], 
            'inputType' => 'text',
            'eval' => ['tl_class'=>'w50'],
        ],
        'modal_autoload' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_modal']['modal_autoload'], 
            'inputType' => 'checkbox',
            'eval' => ['tl_class' => 'w50 clr'],
            'default' => true
        ],
        'modal_autodestroy' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_modal']['modal_autodestroy'], 
            'inputType' => 'checkbox',
            'eval' => ['tl_class' => 'w50 clr'],
        ],
        'modal_refresh' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_modal']['modal_refresh'], 
            'inputType' => 'checkbox',
            'eval' => ['tl_class' => 'w50 clr'],
        ],
    ]
];
