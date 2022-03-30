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
    'label' => ['Compteur animé', 'Générez un compteur animé'], 'contentCategory' => 'SMARTGEAR', 'standardFields' => ['cssID'], 'fields' => [
        'config_legend' => [
            'label' => ['Configuration du compteur'], 'inputType' => 'group',
        ], 'startVal' => [
            'label' => ['Valeur de départ', 'Définissez la valeur de départ du compteur'], 'inputType' => 'text', 'eval' => ['tl_class' => 'w50', 'rgxp' => 'digit', 'mandatory' => true],
        ], 'endVal' => [
            'label' => ['Valeur de fin', 'Définissez la valeur de fin du compteur'], 'inputType' => 'text', 'eval' => ['tl_class' => 'w50', 'rgxp' => 'digit', 'mandatory' => true],
        ], 'decimals' => [
            'label' => ['Nombre de décimales', 'Définissez le nombre de décimales du compteur (0 par défaut)'], 'inputType' => 'text', 'eval' => ['tl_class' => 'w50', 'rgxp' => 'digit', 'minval' => 0],
        ], 'duration' => [
            'label' => ['Durée', 'Définissez durée d\'animation en seconde du compteur (2 par défaut)'], 'inputType' => 'text', 'eval' => ['tl_class' => 'w50', 'rgxp' => 'digit', 'minval' => 0],
        ], 'delay' => [
            'label' => ['Délai', 'Définissez un temps d\'attente en seconde avant déclenchement de l\'animation du compteur (0 par défaut)'], 'inputType' => 'text', 'eval' => ['tl_class' => 'w50', 'rgxp' => 'digit', 'minval' => 0],
        ], 'unit' => [
            'label' => ['Unité', 'Définissez une unité qui sera affichée à côté du compteur (exemple: m²)'], 'inputType' => 'text', 'eval' => ['tl_class' => 'w50'],
        ], 'label' => [
            'label' => ['Label', 'Définissez un court texte qui sera affiché en dessous du compteur'], 'inputType' => 'text', 'eval' => ['tl_class' => 'w50'],
        ],
    ],
];
