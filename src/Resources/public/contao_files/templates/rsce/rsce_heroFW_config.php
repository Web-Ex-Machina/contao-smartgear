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
    'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_herofw'], 'contentCategory' => 'SMARTGEAR', 'standardFields' => ['cssID'], 'fields' => [
        'config_legend' => [
            'label' => [&$GLOBALS['TL_LANG']['tl_content']['config_legend']], 'inputType' => 'group',
        ],
        'force_fullheight' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['force_fullheight'],
            'inputType' => 'checkbox',
            'eval' => ['tl_class' => 'w50'],
        ],
        'block_height' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['block_height'],
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50'],
        ],
        'image_legend' => [
            'label' => [&$GLOBALS['TL_LANG']['tl_content']['image_legend']],
            'inputType' => 'group',
        ],
        'singleSRC' => [
            'inputType' => 'standardField',
        ],
        'alt' => [
            'inputType' => 'standardField',
            'eval' => ['tl_class' => 'w50'],
        ],
        'img_size' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['size'], 'inputType' => 'imageSize', 'reference' => &$GLOBALS['TL_LANG']['MSC'], 'eval' => ['rgxp' => 'natural', 'includeBlankOption' => true, 'nospace' => true, 'helpwizard' => true, 'tl_class' => 'w50 clr'], 'options_callback' => function () {
                return System::getContainer()->get('contao.image.image_sizes')->getOptionsForUser(BackendUser::getInstance());
            },
        ],
        'content_legend' => [
            'label' => [&$GLOBALS['TL_LANG']['tl_content']['rsce_herofw']['content_legend']],
            'inputType' => 'group',
        ],
        'text' => [
            'inputType' => 'standardField',
            'eval' => ['mandatory' => false, 'tl_class' => 'clr'],
        ],
        'url' => [
            'inputType' => 'standardField',
            'eval' => ['mandatory' => false],
        ],
        'linkTitle' => [
            'inputType' => 'standardField',
        ],
        'target' => [
            'label' => &$GLOBALS['TL_LANG']['MSC']['target'], 'inputType' => 'checkbox', 'eval' => ['tl_class' => 'w50'],
        ],
    ],
];
