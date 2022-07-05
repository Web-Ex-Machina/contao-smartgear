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

return [
    'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_pdfviewer'],
    'contentCategory' => 'SMARTGEAR',
    'standardFields' => ['cssID'],
    'fields' => [
        'source' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_pdfviewer']['source'],
            'inputType' => 'fileTree',
            'eval' => ['filesOnly' => true, 'fieldType' => 'radio'],
        ],
        'downloadable' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_pdfviewer']['downloadable'],
            'inputType' => 'checkbox',
            'eval' => ['tl_class' => 'clr'],
        ],
        'linkTitle' => [
            'inputType' => 'standardField',
            'eval' => ['tl_class' => 'clr'],
        ],
        'linkTitle' => [
            'inputType' => 'standardField',
        ],
        'title' => [
            'label'     => &$GLOBALS['TL_LANG']['tl_content']['titleText'], 
            'inputType' => 'text', 
            'eval'      => ['tl_class' => 'w50'],
        ],
        'player_ratio' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_pdfviewer']['player_ratio'],
            'inputType' => 'select',
            'options' => array(
                '' => 'Original',
                'r_16-9' => '16:9',
                'r_4-3'  => '4:3',
                'r_2-1'  => '2:1',
                'r_1-1'  => '1:1',
                'r_1-2'  => '1:2',
            ),
            'eval' => array('tl_class'=>'w50'),
        ),
        'playerSize' => [
            'inputType' => 'standardField', 
        ],
        'center' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_pdfviewer']['center'],
            'inputType' => 'checkbox',
            'eval' => ['tl_class' => 'clr'],
        ],
    ],
];
