<?php

/**
 * rsce_block-img_config.php
 * https://demo.smartgear.webexmachina.fr/guidelines.html
 */
return array(
    'label' => array('Block Card', 'Générez un bloc de contenu composé d\'un texte et d\'une image'),
    'types' => array('content'),
    'contentCategory' => 'Éléments personnalisés',
    'standardFields' => array('cssID'),
    'fields' => array(
        'content_background' => array(
            'label' => array('Couleur de fond', 'Si souhaité, ajustez la couleur de fond'),
            'inputType' => 'select',
            'options' => \WEM\SmartgearBundle\Backend\Util::getSmartgearColors(),
            'eval' => array('tl_class'=>'w50 clr','includeBlankOption'=>true),
        ),
        'content_color' => array(
            'label' => array('Couleur du texte', 'Si souhaité, ajustez la couleur du contenu'),
            'inputType' => 'select',
            'options' => \WEM\SmartgearBundle\Backend\Util::getSmartgearColors("rsce-ft"),
            'eval' => array('tl_class'=>'w50','includeBlankOption'=>true),
        ),
        'content_opacity' => array(
            'label' => array('Opacité du fond', 'Ajustez l\'opacité du fond de l\'item (Valeur entre 0 et 10)'),
            'inputType' => 'text',
            'eval' => array('rgxp' => 'digit', 'tl_class' => 'w50', 'min'=>0, 'max'=>10)
        ),
        'content_order' => array(
            'label' => array('Ordre d\'apparition', 'Ajustez l\'ordre d\'apparition des contenus'),
            'inputType' => 'select',
            'options' => array(
                'img_first' => 'Image > Texte',
                'txt_first' => 'Texte > Image',
            ),
            'eval' => array('tl_class'=>'clr w50'),
        ),
        'text_align' => array(
            'label' => array('Alignement texte', 'Ajustez l\'alignement du texte'),
            'inputType' => 'select',
            'options' => array(
                'txt-center' => 'Centre',
                'txt-right' => 'right',
            ),
            'eval' => array('tl_class'=>'w50','includeBlankOption'=>true),
        ),
        'addRadius' => array(
            'label' => array("Bord arrondis", "Cochez pour arrondir les extrémités du bloc"),
            'inputType' => 'checkbox',
            'eval' => array( 'tl_class' => 'clr w50')
        ),
        'preset_legend' => array(
            'label' => array('Presets'),
            'inputType' => 'group',
        ),
        'preset' => array(
            'label' => array('Preset', ''),
            'inputType' => 'select',
            'options' => array(
                'light' => 'Light',
                'thumbnail' => 'Thumbnail',
                'inline' => 'Inline',
            ),
            'eval' => array('tl_class'=>'w50 clr','includeBlankOption'=>true),
        ),
        'image_legend' => array(
            'label' => array('Image'),
            'inputType' => 'group',
        ),
        'singleSRC' => array(
            'inputType' => 'standardField',
            'eval' => array('tl_class'=>'w50','mandatory'=>false)
        ),
        'fullsize' => array(
            'inputType' => 'standardField',
            'eval' => array('tl_class'=>'clr w50')
        ),
        'alt' => array(
            'inputType' => 'standardField',
            'eval' => array('tl_class'=>'clr w100 long')
        ),
        'size' => array(
            'inputType' => 'standardField',
        ),
        'image_displaymode' => array(
            'label' => array('Mode d\'affichage', 'Change le mode d\'affichage de l\'image dans le bloc'),
            'inputType' => 'select',
            'options' => array(
                'img--cover'    => 'Remplissage automatique',
                'img--contain'  => 'Ajustée',
                'img--natural'  => 'Taille naturelle',
            ),
            'eval' => array('tl_class'=>'w50'),
        ),
        'image_css' => array(
            'label' => array('Classe(s) CSS image', 'Classe(s) CSS à ajouter a l\'image')
            ,'inputType' => 'text'
            ,'eval' => array('tl_class'=>'w50 clr', 'mandatory' => false)
        ),

        'imagesize_legend' => array(
            'label' => array('Image - Remplissage automatique'),
            'inputType' => 'group',
        ),
        'imagesize_ratio' => array(
            'label' => array('Ratio', 'Si souhaité, sélectionnez un ratio d\'image'),
            'inputType' => 'select',
            'options' => array(
                '' => 'Original',
                'r_1-1' => '1:1',
                'r_2-1' => '2:1',
                'r_1-2' => '1:2',
                'r_16-9' => '16:9',
            ),
            'eval' => array('tl_class'=>'w50'),
        ),
        'imagesize_horizontal' => array(
            'label' => array('Alignement Horizontal', 'Si souhaité, ajustez la position horizontale de l\'image'),
            'inputType' => 'select',
            'options' => array(
                '' => 'Aucun',
                'left' => 'Gauche',
                'right' => 'Droite',
            ),
            'eval' => array('tl_class'=>'w50'),
        ),
        'imagesize_vertical' => array(
            'label' => array('Alignement vertical', 'Si souhaité, ajustez la position verticale de l\'image'),
            'inputType' => 'select',
            'options' => array(
                '' => 'Aucun',
                'top' => 'Haut',
                'bottom' => 'Bas',
            ),
            'eval' => array('tl_class'=>'w50'),
        ),

        'imagehover_legend' => array(
            'label' => array('Image - Effets de survol'),
            'inputType' => 'group',
        ),
        'imagehover_zoom' => array(
            'label' => array('Zoom', 'Si souhaité, sélectionnez un effet de zoom'),
            'inputType' => 'select',
            'options' => array(
                '' => 'Aucun',
                'zoomin' => 'Zoom In',
                'zoomout' => 'Zoom Out',
            ),
            'eval' => array('tl_class'=>'w50'),
        ),
        'imagehover_fade' => array(
            'label' => array('Fade', 'Si souhaité, sélectionnez un effet de fondu'),
            'inputType' => 'select',
            'options' => array(
                '' => 'Aucun',
                'fadetogrey' => 'Fade to grey',
                'fadetocolor' => 'Fade to colour',
            ),
            'eval' => array('tl_class'=>'w50'),
        ),

        'content_legend' => array(
            'label' => array('Contenu'),
            'inputType' => 'group',
        ),
        'headline' => array(
            'label' => array('Titre', 'Si souhaité, indiquez un titre')
            ,'inputType' => 'standardField'
            ,'eval' => array('tl_class' => 'w50', 'mandatory' => false,'allowHtml'=>true,'includeBlankOption'=>true)
        ),
        'text' => array(
            'inputType' => 'standardField',
            'eval' => array('tl_class'=>'clr','mandatory'=>false),
        ),
        'title_css' => array(
            'label' => array('Classe(s) CSS titre', 'Classe(s) CSS à ajouter au titre')
            ,'inputType' => 'text'
            ,'eval' => array('tl_class'=>'w50 clr', 'mandatory' => false)
        ),
        'text_css' => array(
            'label' => array('Classe(s) CSS texte', 'Classe(s) CSS à ajouter au texte')
            ,'inputType' => 'text'
            ,'eval' => array('tl_class'=>'w50 ', 'mandatory' => false)
        ),
        'link_legend' => array(
            'label' => array('Lien'),
            'inputType' => 'group',
        ),
        'url' => array(
            'inputType' => 'standardField',
            'eval' => array('mandatory'=>false)
        ),
        'linkTitle' => array(
            'inputType' => 'standardField',
            'eval' => array('allowHtml'=>true)
        ),
        'target' => array(
            'label' => &$GLOBALS['TL_LANG']['MSC']['target']
            ,'inputType' => 'checkbox'
            ,'eval' => array('tl_class'=>'w50')
        ),
        'link_mode' => array(
            'label' => array('Mode', 'Change le mode d\'affichage du lien dans le bloc'),
            'inputType' => 'select',
            'options' => array(
                'wrapper' => 'Click sur le block',
                'btn' => 'Bouton',
                'link' => 'Lien texte',
            ),
            'eval' => array('tl_class'=>'w50 clr'),
        ),
        'link_css' => array(
            'label' => array('Classe(s) CSS lien', 'Classe(s) CSS à ajouter au lien')
            ,'inputType' => 'text'
            ,'eval' => array('tl_class'=>'w50 ', 'mandatory' => false)
        ),
    ),
);
