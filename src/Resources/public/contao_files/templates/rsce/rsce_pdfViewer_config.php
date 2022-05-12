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
    'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_pdfviewerfw'],
    'contentCategory' => 'SMARTGEAR',
    'standardFields' => ['cssID'],
    'fields' => [
        'source' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_pdfviewerfw']['source'],
            'inputType' => 'fileTree',
            'eval' => ['filesOnly' => true, 'fieldType' => 'radio'],
        ],
        'downloadable' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_pdfviewerfw']['downloadable'],
            'inputType' => 'checkbox',
            // 'options' => ['true'],
            'eval' => ['tl_class' => 'w50 clr'],
        ],
        'download_button_text' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_pdfviewerfw']['download_button_text'],
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50'],
        ],
        'download_button_title' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_pdfviewerfw']['download_button_title'],
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w100 clr'],
        ],
    ],
];
