<?php

/**
 * rsce_block-img_config.php
 * https://demo.smartgear.webexmachina.fr/guidelines.html
 */
return array
(
    'label' => array('Image ++', 'Générez un bloc Image disposant de davantage d\'options'),
    'types' => array('content'),
    'contentCategory' => 'texts',
    'standardFields' => array('cssID'),
    'fields' => array
    (
        'image_legend' => array
        (
            'label' => array('Image'),
            'inputType' => 'group',
        ),   
        'singleSRC' => array
        (
            'inputType' => 'standardField',
        ),
        'alt' => array
        (
            'inputType' => 'standardField',
            'eval' => array('tl_class'=>'w100 long')
        ),
        'imageUrl' => array
        (
            'inputType' => 'standardField',
        ),
        'fullsize' => array
        (
            'inputType' => 'standardField',
        ),

        'imagesize_legend' => array
        (
            'label' => array('Image - Redimensionnement et Positionnement'),
            'inputType' => 'group',
        ),
        'size' => array
        (
            'inputType' => 'standardField',
        ),
        'imagesize_ratio' => array
        (
            'label' => array('Ratio', 'Si souhaité, sélectionnez un ratio d\'image'),
            'inputType' => 'select',
            'options' => array
            (
                '' => 'Original',
                'r_1-1' => '1:1',
                'r_2-1' => '2:1',
                'r_1-2' => '1:2',
                'r_16-9' => '16:9',
            ),
            'eval' => array('tl_class'=>'w50'),
        ),
        'imagesize_horizontal' => array
        (
            'label' => array('Alignement Horizontal', 'Si souhaité, ajustez la position horizontale de l\'image'),
            'inputType' => 'select',
            'options' => array
            (
                '' => 'Aucun',
                'left' => 'Gauche',
                'right' => 'Droite',
            ),
            'eval' => array('tl_class'=>'w50'),
        ),
        'imagesize_vertical' => array
        (
            'label' => array('Alignement vertical', 'Si souhaité, ajustez la position verticale de l\'image'),
            'inputType' => 'select',
            'options' => array
            (
                '' => 'Aucun',
                'top' => 'Haut',
                'bottom' => 'Bas',
            ),
            'eval' => array('tl_class'=>'w50'),
        ),

        'imagehover_legend' => array
        (
            'label' => array('Image - Effets de survol'),
            'inputType' => 'group',
        ),
        'imagehover_zoom' => array
        (
            'label' => array('Zoom', 'Si souhaité, sélectionnez un effet de zoom'),
            'inputType' => 'select',
            'options' => array
            (
                '' => 'Aucun',
                'zoomin' => 'Zoom In',
                'zoomout' => 'Zoom Out',
            ),
            'eval' => array('tl_class'=>'w50'),
        ),
        'imagehover_fade' => array
        (
            'label' => array('Fade', 'Si souhaité, sélectionnez un effet de fondu'),
            'inputType' => 'select',
            'options' => array
            (
                '' => 'Aucun',
                'fadetogrey' => 'Fade to grey',
                'fadetocolor' => 'Fade to colour',
            ),
            'eval' => array('tl_class'=>'w50'),
        ),

        'content_legend' => array
        (
            'label' => array('Contenu'),
            'inputType' => 'group',
        ),   
        'text' => array
        (
            'inputType' => 'standardField',
            'eval' => array('mandatory'=>false),
        ),
        'content_opacity' => array
        (
            'label' => array('Opacité', 'Ajustez, si souhaité, l\'opacité du contenu de l\'item (Valeur entre 0 et 10)'),
            'inputType' => 'text',
            'eval' => array('rgxp' => 'digit', 'tl_class' => 'w50', 'min'=>0, 'max'=>10)
        ),
        'content_position' => array
        (
            'label' => array('Position', 'Si souhaité, ajustez la position du contenu'),
            'inputType' => 'select',
            'options' => array
            (
                '' => 'En dehors de l\'image',
                'inner' => 'Dans l\'image',
                'full' => 'Dans la totalité de l\'image',
            ),
            'eval' => array('tl_class'=>'w50'),
        ),
        'content_horizontal' => array
        (
            'label' => array('Alignement Horizontal', 'Si souhaité, ajustez la position horizontale du contenu'),
            'inputType' => 'select',
            'options' => array
            (
                '' => 'Aucun',
                'left' => 'Gauche',
                'right' => 'Droite',
            ),
            'eval' => array('tl_class'=>'w50'),
        ),
        'content_vertical' => array
        (
            'label' => array('Alignement vertical', 'Si souhaité, ajustez la position verticale du contenu'),
            'inputType' => 'select',
            'options' => array
            (
                '' => 'Aucun',
                'top' => 'Haut',
                'bottom' => 'Bas',
            ),
            'eval' => array('tl_class'=>'w50'),
        ),

        'contenthover_legend' => array
        (
            'label' => array('Contenu - Effets de survol'),
            'inputType' => 'group',
        ),
        'contenthover_legend_translate' => array
        (
            'label' => array('Translation', 'Si souhaité, sélectionnez un effet de translation'),
            'inputType' => 'select',
            'options' => array
            (
                '' => 'Aucun',
                'fromtop' => 'Venant du haut',
                'frombottom' => 'Venant du bas',
                'fromleft' => 'Venant de la gauche',
                'fromright' => 'Venant de la droite',
            ),
            'eval' => array('tl_class'=>'w50'),
        ),
        'contenthover_legend_fade' => array
        (
            'label' => array('Fade', 'Si souhaité, sélectionnez un effet de fondu'),
            'inputType' => 'select',
            'options' => array
            (
                '' => 'Aucun',
                'fadein' => 'Apparait au survol',
                'fadeout' => 'Disparait au survol',
            ),
            'eval' => array('tl_class'=>'w50'),
        ),
    ),
);