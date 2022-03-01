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
    'label' => ['Image ++', 'Générez un bloc Image disposant de davantage d\'options'],
    'contentCategory' => 'SMARTGEAR',
    'standardFields' => ['cssID'],
    'fields' => [
        'image_legend' => [
            'label' => ['Image'],
            'inputType' => 'group',
        ],
        'singleSRC' => [
            'inputType' => 'standardField',
        ],
        'alt' => [
            'inputType' => 'standardField',
            'eval' => ['tl_class' => 'w100 long'],
        ],
        'imageUrl' => [
            'inputType' => 'standardField',
        ],
        'fullsize' => [
            'inputType' => 'standardField',
        ],

        'imagesize_legend' => [
            'label' => ['Image - Redimensionnement et Positionnement'],
            'inputType' => 'group',
        ],
        'size' => [
            'inputType' => 'standardField',
        ],
        'imagesize_ratio' => [
            'label' => ['Ratio', 'Si souhaité, sélectionnez un ratio d\'image'],
            'inputType' => 'select',
            'options' => [
                '' => 'Original',
                'r_1-1' => '1:1',
                'r_2-1' => '2:1',
                'r_1-2' => '1:2',
                'r_16-9' => '16:9',
            ],
            'eval' => ['tl_class' => 'w50'],
        ],
        'imagesize_horizontal' => [
            'label' => ['Alignement Horizontal', 'Si souhaité, ajustez la position horizontale de l\'image'],
            'inputType' => 'select',
            'options' => [
                '' => 'Aucun',
                'left' => 'Gauche',
                'right' => 'Droite',
            ],
            'eval' => ['tl_class' => 'w50'],
        ],
        'imagesize_vertical' => [
            'label' => ['Alignement vertical', 'Si souhaité, ajustez la position verticale de l\'image'],
            'inputType' => 'select',
            'options' => [
                '' => 'Aucun',
                'top' => 'Haut',
                'bottom' => 'Bas',
            ],
            'eval' => ['tl_class' => 'w50'],
        ],

        'imagehover_legend' => [
            'label' => ['Image - Effets de survol'],
            'inputType' => 'group',
        ],
        'imagehover_zoom' => [
            'label' => ['Zoom', 'Si souhaité, sélectionnez un effet de zoom'],
            'inputType' => 'select',
            'options' => [
                '' => 'Aucun',
                'zoomin' => 'Zoom In',
                'zoomout' => 'Zoom Out',
            ],
            'eval' => ['tl_class' => 'w50'],
        ],
        'imagehover_fade' => [
            'label' => ['Fade', 'Si souhaité, sélectionnez un effet de fondu'],
            'inputType' => 'select',
            'options' => [
                '' => 'Aucun',
                'fadetogrey' => 'Fade to grey',
                'fadetocolor' => 'Fade to colour',
            ],
            'eval' => ['tl_class' => 'w50'],
        ],

        'content_legend' => [
            'label' => ['Contenu'],
            'inputType' => 'group',
        ],
        'text' => [
            'inputType' => 'standardField',
            'eval' => ['mandatory' => false],
        ],
        'content_background' => [
            'label' => ['Fond du texte', 'Si souhaité, ajustez le fond du contenu'],
            'inputType' => 'select',
            'options' => \WEM\SmartgearBundle\Classes\Util::getSmartgearColors(),
            'eval' => ['tl_class' => 'w50 clr'],
        ],
        'content_color' => [
            'label' => ['Couleur du texte', 'Si souhaité, ajustez la couleur du contenu'],
            'inputType' => 'select',
            'options' => \WEM\SmartgearBundle\Classes\Util::getSmartgearColors('rsce-ft'),
            'eval' => ['tl_class' => 'w50'],
        ],
        'content_opacity' => [
            'label' => ['Opacité', 'Ajustez, si souhaité, l\'opacité du contenu de l\'item (Valeur entre 0 et 10)'],
            'inputType' => 'text',
            'eval' => ['rgxp' => 'digit', 'tl_class' => 'w50', 'min' => 0, 'max' => 10],
        ],
        'content_position' => [
            'label' => ['Position', 'Si souhaité, ajustez la position du contenu'],
            'inputType' => 'select',
            'options' => [
                '' => 'En dehors de l\'image',
                'inner' => 'Dans l\'image',
                'full' => 'Dans la totalité de l\'image',
            ],
            'eval' => ['tl_class' => 'w50'],
        ],
        'content_horizontal' => [
            'label' => ['Alignement Horizontal', 'Si souhaité, ajustez la position horizontale du contenu'],
            'inputType' => 'select',
            'options' => [
                '' => 'Aucun',
                'left' => 'Gauche',
                'right' => 'Droite',
            ],
            'eval' => ['tl_class' => 'w50'],
        ],
        'content_vertical' => [
            'label' => ['Alignement vertical', 'Si souhaité, ajustez la position verticale du contenu'],
            'inputType' => 'select',
            'options' => [
                '' => 'Aucun',
                'top' => 'Haut',
                'bottom' => 'Bas',
            ],
            'eval' => ['tl_class' => 'w50'],
        ],

        'contenthover_legend' => [
            'label' => ['Contenu - Effets de survol'],
            'inputType' => 'group',
        ],
        'contenthover_legend_translate' => [
            'label' => ['Translation', 'Si souhaité, sélectionnez un effet de translation'],
            'inputType' => 'select',
            'options' => [
                '' => 'Aucun',
                'fromtop' => 'Venant du haut',
                'frombottom' => 'Venant du bas',
                'fromleft' => 'Venant de la gauche',
                'fromright' => 'Venant de la droite',
            ],
            'eval' => ['tl_class' => 'w50'],
        ],
        'contenthover_legend_fade' => [
            'label' => ['Fade', 'Si souhaité, sélectionnez un effet de fondu'],
            'inputType' => 'select',
            'options' => [
                '' => 'Aucun',
                'fadein' => 'Apparait au survol',
                'fadeout' => 'Disparait au survol',
            ],
            'eval' => ['tl_class' => 'w50'],
        ],
    ],
];
