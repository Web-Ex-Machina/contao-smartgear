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
    'label' => ['Blocs de prix', 'Générez une liste de blocs de prix'],
    'contentCategory' => 'SMARTGEAR',
    'standardFields' => ['cssID'],
    'fields' => [
        'listItems' => [
            'label' => ['Price Cards', 'Editez les cards'], 'elementLabel' => '%s. price card', 'inputType' => 'list', 'fields' => [
                'price_legend' => [
                    'label' => ['Prix'], 'inputType' => 'group',
                ],
                'amount' => [
                    'label' => ['Prix', 'Saisissez le prix/nombre'], 'inputType' => 'text', 'eval' => ['tl_class' => 'clr w50'],
                ],
                'currency' => [
                    'label' => ['Devise', 'Saisissez la devise/unité (€,$,£,...)'], 'inputType' => 'text', 'eval' => ['tl_class' => 'clr w50'],
                ],
                'period' => [
                    'label' => ['Récurrence', 'Saisissez la récurrence (par mois, par jour,...)'], 'inputType' => 'text', 'eval' => ['tl_class' => 'clr w50'],
                ],
                'content_legend' => [
                    'label' => ['Contenus'], 'inputType' => 'group',
                ],
                'lines' => [
                    'label' => ['Lignes', 'Ajouter x lignes de contenus (html autorisé)'], 'inputType' => 'listWizard', 'eval' => ['tl_class' => 'clr w50', 'allowHtml' => true],
                ],
                'cta_text' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['linkTitle'], 'inputType' => 'text', 'eval' => ['tl_class' => 'w50 clr', 'mandatory' => false],
                ],
                'cta_href' => [
                    'label' => &$GLOBALS['TL_LANG']['MSC']['url'], 'inputType' => 'text', 'eval' => ['rgxp' => 'url', 'tl_class' => 'w50 wizard '], 'wizard' => [['tl_content', 'pagePicker']],
                ],
                'cta_title' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['titleText'], 'inputType' => 'text', 'eval' => ['tl_class' => 'w50'],
                ],
                'cta_target' => [
                    'label' => &$GLOBALS['TL_LANG']['MSC']['target'], 'inputType' => 'checkbox', 'eval' => ['tl_class' => 'w50'],
                ],
                'cta_classes' => [
                    'label' => ['Classes supplémentaires', 'Indiquez, si souhaité, la ou les classes css à ajouter au bouton'], 'inputType' => 'text', 'eval' => ['tl_class' => 'w50 clr'],
                ],
                'style_legend' => [
                    'label' => ['Apparence'], 'inputType' => 'group',
                ],
                'isMain' => [
                    'label' => ['Vedette', 'Met en valeur le bloc'], 'inputType' => 'checkbox', 'eval' => ['tl_class' => 'w50'],
                ],
            ],
        ],
    ],
];
