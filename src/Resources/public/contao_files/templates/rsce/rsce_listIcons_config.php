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
    'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_listicons'],
    'types' => ['content'],
    'contentCategory' => 'texts',
    'standardFields' => ['cssID'],
    'fields' => [
        'listItems' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_listicons']['items_legend'], 'elementLabel' => &$GLOBALS['TL_LANG']['tl_content']['rsce_listicons']['item_legend'], 'inputType' => 'list', 'fields' => [
                'img_src' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_listicons']['img_src'], 'inputType' => 'fileTree', 'eval' => ['filesOnly' => true, 'fieldType' => 'radio', 'extensions' => Config::get('validImageTypes'), 'tl_class' => 'w50'],
                ],
                'img_text' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_listicons']['img_text'], 'inputType' => 'text', 'eval' => ['tl_class' => 'clr', 'allowHtml' => true],
                ],
                'text' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_listicons']['text'], 'inputType' => 'textarea', 'eval' => ['rte' => 'tinyMCE', 'tl_class' => 'clr', 'mandatory' => true],
                ],
                'href' => [
                    'label' => &$GLOBALS['TL_LANG']['MSC']['url'], 'inputType' => 'text', 'eval' => ['rgxp' => 'url', 'tl_class' => 'w50 wizard clr'], 'wizard' => [['tl_content', 'pagePicker']],
                ],
                'title' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['titleText'], 'inputType' => 'text', 'eval' => ['tl_class' => 'w50'],
                ],
                'target' => [
                    'label' => &$GLOBALS['TL_LANG']['MSC']['target'], 'inputType' => 'checkbox', 'eval' => ['tl_class' => 'w50'],
                ],
                'classes' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_listicons']['classes'], 'inputType' => 'text', 'eval' => ['tl_class' => 'w50 clr'],
                ],
            ],
        ],
    ],
];
