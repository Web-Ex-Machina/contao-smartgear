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
    'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_quoteWrating'],
    'types' => ['content'],
    'contentCategory' => 'texts',
    'standardFields' => ['cssID'],
    'fields' => [
        'content_legend' => [
            'label' => [&$GLOBALS['TL_LANG']['tl_content']['content_legend']],
            'inputType' => 'group',
        ],
        'rating' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_quote']['rating'], 
            'inputType' => 'text', 
            'eval' => ['tl_class'=>'w50 clr', 'mandatory' => false, 'rgxp' => 'digit', 'minval' => 0, 'maxval' => 5],
        ],
        'text' => [
            'inputType' => 'standardField',
            'eval' => ['mandatory' => true, 'tl_class' => 'clr'],
        ],
        'author' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_quote']['author'], 
            'inputType' => 'text', 
            'eval' => ['tl_class' => 'w50 clr', 'mandatory' => false],
        ],
        'author_position' => [
            'label' => &$GLOBALS['TL_LANG']['tl_content']['rsce_quote']['author_position'], 
            'inputType' => 'text', 
            'eval' => ['tl_class' => 'w50 clr', 'mandatory' => false],
        ],
    ],
];
