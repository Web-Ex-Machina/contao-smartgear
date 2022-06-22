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
    'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_quote'],
    'types' => ['content'],
    'contentCategory' => 'texts',
    'standardFields' => ['cssID'],
    'fields' => [
        'image_legend' => [
            'label' => [&$GLOBALS['TL_LANG']['tl_content']['image_legend']],
            'inputType' => 'group',
        ],
        'singleSRC' => [
            'inputType' => 'standardField',
            'eval' => ['mandatory' => false],
        ],
        'alt' => [
            'inputType' => 'standardField',
            'eval' => ['tl_class' => 'w100 long'],
        ],
        'size' => [
            'inputType' => 'standardField',
        ],
        'image_pos' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_quote']['image_pos'],
            'inputType' => 'select',
            'options' => [
                'before' => &$GLOBALS['TL_LANG']['tl_content']['rsce_quote']['image_pos']['optionBefore'],
                'after' => &$GLOBALS['TL_LANG']['tl_content']['rsce_quote']['image_pos']['optionAfter'],
            ],
            'eval' => ['tl_class' => 'w50'],
        ],
        'content_legend' => [
            'label' => [&$GLOBALS['TL_LANG']['tl_content']['rsce_quote']['content_legend']],
            'inputType' => 'group',
        ],
        'text' => [
            'inputType' => 'standardField',
            'eval' => ['mandatory' => true, 'tl_class' => 'clr'],
        ],
        'author' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_quote']['author'], 'inputType' => 'text', 'eval' => ['tl_class' => 'w50 clr', 'mandatory' => false],
        ],
    ],
];
