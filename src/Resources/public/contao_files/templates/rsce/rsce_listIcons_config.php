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
    'label' => ['Liste d\'icônes', 'Générez une liste d\'icones accompagnées de texte.'],
    'types' => ['content'],
    'contentCategory' => 'texts',
    'standardFields' => ['cssID'],
    'fields' => [
        'listItems' => [
            'label' => ['Vignettes', 'Editez les vignettes'], 'elementLabel' => '%s. vignette', 'inputType' => 'list', 'fields' => [
                'img_src' => [
                    'label' => ['Image', 'Sélectionnez une icone'], 'inputType' => 'fileTree', 'eval' => ['filesOnly' => true, 'fieldType' => 'radio', 'extensions' => Config::get('validImageTypes'), 'tl_class' => 'w50'],
                ],
                'img_text' => [
                    'label' => ['Icone font-awesome (désactive l\'image sélectionnée)', 'Indiquez le code html de l\'icone désirée (exemple: &lt;i class="fas fa-paper-plane"&gt;&lt;/i&gt;) voir site <a href="https://fontawesome.com/icons?d=gallery" target="_blank">Font Awesome =></a>'], 'inputType' => 'text', 'eval' => ['tl_class' => 'clr', 'allowHtml' => true],
                ],
                'text' => [
                    'label' => ['Texte', 'Saisissez le texte affiché en dessous de l\'icone'], 'inputType' => 'textarea', 'eval' => ['rte' => 'tinyMCE', 'tl_class' => 'clr', 'mandatory' => true],
                ],
                'href' => [
                    'label' => &$GLOBALS['TL_LANG']['MSC']['url'], 'inputType' => 'text', 'eval' => ['rgxp' => 'url', 'tl_class' => 'w50 wizard clr'], 'wizard' => [['tl_content', 'pagePicker']],
                ],
                'title' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['titleText'], 'inputType' => 'text', 'eval' => ['tl_class' => 'w50'],
                ],
                'target' => [
                    'label' => &$GLOBALS['TL_LANG']['MSC']['target'], 'inputType' => 'checkbox', 'eval' => ['tl_class' => 'w50'],
                ],
                'classes' => [
                    'label' => ['Classes supplémentaires', 'Indiquez, si souhaité, la ou les classes css à ajouter à l\'item'], 'inputType' => 'text', 'eval' => ['tl_class' => 'w50 clr'],
                ],
            ],
        ],
    ],
];
