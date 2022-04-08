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
    'standardFields' => ['cssID'],
    'fields' => [
        'source' => [
            'label' => ['Fichier PDF', 'Sélectionnez le fichier PDF à afficher'],
            'inputType' => 'fileTree',
            'eval' => ['filesOnly' => true, 'fieldType' => 'radio'],
        ],
        'downloadable' => [
            'label' => ['Fichier téléchargeable', 'Définissez si le PDF peut être téléchargé'],
            'inputType' => 'radio',
            'options' => ['true'],
            'eval' => ['tl_class' => 'w50 clr'],
        ],
        'download_button_text' => [
            'label' => ['Texte du bouton', 'Définissez le texte du bouton'],
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50'],
        ],
        'download_button_title' => [
            'label' => ['Titre du bouton', 'Définissez le texte affiché au survol du bouton'],
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w100 clr'],
        ],
    ],
];
