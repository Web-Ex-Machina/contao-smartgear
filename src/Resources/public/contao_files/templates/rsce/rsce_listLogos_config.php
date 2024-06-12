<?php

declare(strict_types=1);

/**
 * SMARTGEAR for Contao Open Source CMS
 * Copyright (c) 2015-2023 Web ex Machina
 *
 * @category ContaoBundle
 * @package  Web-Ex-Machina/contao-smartgear
 * @author   Web ex Machina <contact@webexmachina.fr>
 * @link     https://github.com/Web-Ex-Machina/contao-smartgear/
 */

return [
    'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_listlogos'],
    'types' => ['content'],
    'contentCategory' => 'miscellaneous',
    'standardFields' => ['cssID'],
    'fields' => [
        'listItems' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_listlogos']['items_legend'],
            'elementLabel' => &$GLOBALS['TL_LANG']['tl_content']['rsce_listlogos']['item_legend'],
            'inputType' => 'list',
            'fields' => [
                'image_src' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['singleSRC'],
                    'inputType' => 'fileTree',
                    'eval' => ['filesOnly' => true, 'fieldType' => 'radio', 'extensions' => \Contao\Config::get('validImageTypes'), 'tl_class' => 'w50 clr'],
                ],
                'image_alt' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['alt'], 'inputType' => 'text', 'eval' => ['tl_class' => 'w100 long clr'],
                ],
                'image_size' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['size'],
                    'inputType' => 'imageSize',
                    'reference' => &$GLOBALS['TL_LANG']['MSC'],
                    'eval' => ['rgxp' => 'natural', 'includeBlankOption' => true, 'nospace' => true, 'helpwizard' => true, 'tl_class' => 'w50 clr'],
                    'options_callback' => fn() => \Contao\System::getContainer()->get('contao.image.image_sizes')->getOptionsForUser(\Contao\BackendUser::getInstance()),
                ],
                'image_css' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['image_css'], 'inputType' => 'text', 'eval' => ['tl_class' => 'w50', 'mandatory' => false],
                ],
                'background_legend' => [
                    'label' => [&$GLOBALS['TL_LANG']['tl_content']['background_legend']],
                    'inputType' => 'group',
                ],
                'image_background' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_listicons']['image_background'],
                    'inputType' => 'select',
                    'options_callback' => fn($dc) => \WEM\SmartgearBundle\Classes\Util::getSmartgearColors($dc->table, (int) $dc->id),
                    'eval' => ['tl_class' => 'w50 clr', 'includeBlankOption' => true],
                ],
                'link_legend' => [
                    'label' => [&$GLOBALS['TL_LANG']['tl_content']['link_legend']],
                    'inputType' => 'group',
                ],
                'href' => [
                    'label' => &$GLOBALS['TL_LANG']['MSC']['url'],
                    'inputType' => 'text',
                    'eval' => ['rgxp' => 'url', 'tl_class' => 'w50 wizard clr'],
                    'wizard' => [['tl_content', 'pagePicker']],
                ],
                'title' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['titleText'],
                    'inputType' => 'text',
                    'eval' => ['tl_class' => 'w50'],
                ],
                'target' => [
                    'label' => &$GLOBALS['TL_LANG']['MSC']['target'],
                    'inputType' => 'checkbox',
                    'eval' => ['tl_class' => 'w50'],
                ],
                'classes' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_listicons']['classes'],
                    'inputType' => 'text',
                    'eval' => ['tl_class' => 'w50 clr'],
                ],
            ],
        ],
    ],
];
