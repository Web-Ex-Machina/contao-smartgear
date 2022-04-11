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
    'label' => ['Citation', 'Générez un block citation, avec ou sans photo'],
    'types' => ['content'],
    'contentCategory' => 'texts',
    'standardFields' => ['cssID'],
    'fields' => [
        'image_legend' => [
            'label' => ['Image'],
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
            'label' => ['Positionnement', 'Sélectionnez la position de l\'image (gauche ou droite de la citation)'],
            'inputType' => 'select',
            'options' => [
                'before' => 'Gauche',
                'after' => 'Droite',
            ],
            'eval' => ['tl_class' => 'w50'],
        ],
        'content_legend' => [
            'label' => ['Contenu'],
            'inputType' => 'group',
        ],
        'text' => [
            'inputType' => 'standardField',
            'eval' => ['mandatory' => false, 'tl_class' => 'clr'],
        ],
        'author' => [
            'label' => ['Auteur', 'Si souhaité, indiquez l\'auteur de la citation'], 'inputType' => 'text', 'eval' => ['tl_class' => 'w50 clr', 'mandatory' => false],
        ],
    ],
];
