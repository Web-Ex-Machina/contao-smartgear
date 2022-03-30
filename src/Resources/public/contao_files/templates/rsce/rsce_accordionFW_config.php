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
    'label' => ['Accordéon (seul)', 'Générez un accordéon et configurez les éléments'], 'contentCategory' => 'accordion', 'standardFields' => ['cssID'], 'fields' => [
        'config_legend' => [
            'label' => ['Configuration de l\'accordéon'], 'inputType' => 'group',
        ], 'deploy_all' => [
            'label' => ['Tout déployer', 'Cochez pour déployer automatiquement tous les éléments'], 'inputType' => 'checkbox', 'eval' => ['tl_class' => 'w50 clr'],
        ], 'disable_collapse' => [
            'label' => ['Désactiver déployer/replier', 'Cochez pour désactiver les actions déployer/replier (déploie automatiquement tous les éléments)'], 'inputType' => 'checkbox', 'eval' => ['tl_class' => 'w50'],
        ], 'auto_collapse' => [
            'label' => ['Auto repliage', "Cochez pour replier automatiquement les éléments lors du déploiement de l'un d'eux"], 'inputType' => 'checkbox', 'eval' => ['tl_class' => 'w50'],
        ]

        // Items
        , 'items' => [
            'label' => ['Eléments', 'Editez les éléments'], 'elementLabel' => '%s. élément', 'inputType' => 'list', 'fields' => [
                // Content
                'title' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['headline'], 'inputType' => 'text', 'eval' => ['tl_class' => 'w50', 'mandatory' => true],
                ], 'hl_title' => [
                    'label' => ['Format titre', 'Selectionner un format de titre a appliquer'],
                    'inputType' => 'select',
                    'options' => [
                        '' => 'Aucun',
                        'h1' => 'H1',
                        'h2' => 'H2',
                        'h3' => 'H3',
                        'h4' => 'H4',
                        'h5' => 'H5',
                        'h6' => 'H6',
                    ],
                    'eval' => ['tl_class' => 'w50 clr', 'mandatory' => false],
                ], 'content' => [
                    'label' => ['Contenu', 'Saisissez le contenu textuel de l\'élément'], 'inputType' => 'textarea', 'eval' => ['rte' => 'tinyMCE', 'tl_class' => 'clr', 'mandatory' => true],
                ], 'lock' => [
                    'label' => ['Lock', "Cochez pour que l'élément soit toujours visible (désactive les actions déployer/replier)"], 'inputType' => 'checkbox', 'eval' => ['tl_class' => 'w50'],
                ], 'active' => [
                    'label' => ['Active', "Cochez pour que l'élément soit déployé automatiquement au chargement de la page"], 'inputType' => 'checkbox', 'eval' => ['tl_class' => 'w50'],
                ],
            ],
        ],
    ],
];
