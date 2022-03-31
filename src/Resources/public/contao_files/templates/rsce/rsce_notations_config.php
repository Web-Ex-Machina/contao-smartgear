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
    'label' => ['Notes', 'Générez une liste notations sous forme d\'étoiles'],
    'contentCategory' => 'SMARTGEAR',
    'standardFields' => ['cssID'/*,'headline'*/],
    'fields' => [
        'noteMax' => [
            'label' => ['Note maximale', 'Définissez la note maximale possible.'],
            'inputType' => 'text',
            'default' => 5,
            'eval' => ['tl_class' => 'w50 clr', 'rgxp' => 'digit'],
        ],
        'notations' => [
            'label' => ['Notes', 'Editez les notes'],
            'elementLabel' => '%s. note',
            'inputType' => 'list',
            'fields' => [
                'label' => [
                    'label' => ['Label', 'Saisissez le label de la note'],
                    'inputType' => 'text',
                    'eval' => ['tl_class' => 'clr w50', 'mandatory' => true],
                ],
                'note' => [
                    'label' => ['Note', 'Saisissez la note'],
                    'inputType' => 'text',
                    'eval' => ['tl_class' => 'w50', 'rgxp' => 'digit', 'mandatory' => true],
                ],
            ],
        ],
        'text' => [
            'label' => ['Texte supplémentaire', 'Saisissez un court texte placé après les notes'], 'inputType' => 'textarea', 'eval' => ['rte' => 'tinyMCE', 'tl_class' => 'clr'],
        ],
    ],
];
