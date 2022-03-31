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
    'label' => ['Affichage PDF', 'Bloc d\'affichage de PDF'],
    'contentCategory' => 'SMARTGEAR',
    'standardFields' => ['cssID', 'headline'],
    'fields' => [
        'source' => [
            'label' => ['Fichier PDF', 'Sélectionnez le fichier PDF à afficher'],
            'inputType' => 'fileTree',
            'eval' => ['filesOnly' => true, 'fieldType' => 'radio'],
        ],
        'downloadable' => [
            'label' => ['Fichier téléchargeable', 'Définissez si le PDF peut être téléchargé'],
            'inputType' => 'radio',
            'eval' => ['tl_class' => 'w50 clr'],
        ],
        'download_button_type' => [
            'label' => ['Type de bouton', 'Définissez si le type de bouton'],
            'eval' => ['tl_class' => 'w50'],
        ],
        'download_button_text' => [
            'label' => ['Texte du bouton', 'Définissez si le texte du bouton'],
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50'],
        ],
        'download_button_title' => [
            'label' => ['Titre du bouton', 'Définissez si le texte affiché au survol du bouton'],
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w100'],
        ],
        // 'noteMax' => [
        //     'label' => ['Note maximale', 'Définissez la note maximale possible.'],
        //     'inputType' => 'text',
        //     'default' => 5,
        //     'eval' => ['tl_class' => 'w50 clr', 'rgxp' => 'digit'],
        // ],
        // 'notations' => [
        //     'label' => ['Notes', 'Editez les notes'],
        //     'elementLabel' => '%s. note',
        //     'inputType' => 'list',
        //     'fields' => [
        //         'label' => [
        //             'label' => ['Label', 'Saisissez le label de la note'],
        //             'inputType' => 'text',
        //             'eval' => ['tl_class' => 'clr w50', 'mandatory' => true],
        //         ],
        //         'note' => [
        //             'label' => ['Note', 'Saisissez la note'],
        //             'inputType' => 'text',
        //             'eval' => ['tl_class' => 'w50', 'rgxp' => 'digit', 'mandatory' => true],
        //         ],
        //     ],
        // ],
        // 'text' => [
        //     'label' => ['Texte supplémentaire', 'Saisissez un court texte placé après les notes'], 'inputType' => 'textarea', 'eval' => ['rte' => 'tinyMCE', 'tl_class' => 'clr'],
        // ],
    ],
];
