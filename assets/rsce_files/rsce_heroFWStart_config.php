<?php
// rsce_heroFW_config.php
return array(
    'label' => array('Hero FW -- Wrapper Start', 'Générez un élément stylisé composé d\'un texte sur une image')
    ,'contentCategory' => 'SMARTGEAR'
    ,'standardFields' => array('cssID')
    ,'fields' => array(
        'config_legend' => array(
            'label' => array('Configuration du bloc')
            ,'inputType' => 'group'
        )
        ,'force_fullwidth' => array(
            'label' => array("Toute la largeur", "Cochez pour forcer le bloc à prendre toute la largeur de l'écran"),
            'inputType' => 'checkbox',
            'eval' => array( 'tl_class' => 'w50')
        ),
        'force_fullheight' => array(
            'label' => array("Toute la hauteur", "Cochez pour forcer le bloc à prendre toute la hauteur de l'écran"),
            'inputType' => 'checkbox',
            'eval' => array( 'tl_class' => 'w50')
        ),
        'block_height' => array(
            'label' => array("Hauteur de l'élément", "Indiquez la hauteur voulue de l'élément"),
            'inputType' => 'text',
            'eval' => array( 'tl_class' => 'w50')
        ),

        'image_legend' => array(
            'label' => array('Image'),
            'inputType' => 'group',
        ),
        'singleSRC' => array(
            'inputType' => 'standardField',
        ),
        'alt' => array(
            'inputType' => 'standardField',
            'eval' => array('tl_class'=>'w50')
        ),
        'img_size' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_content']['size']
            ,'inputType' => 'imageSize'
            ,'reference' => &$GLOBALS['TL_LANG']['MSC']
            ,'eval'      => array('rgxp'=>'natural', 'includeBlankOption'=>true, 'nospace'=>true, 'helpwizard'=>true, 'tl_class'=>'w50 clr')
            ,'options_callback' => function () {
                return System::getContainer()->get('contao.image.image_sizes')->getOptionsForUser(BackendUser::getInstance());
            }
        ),
        'image_opacity' => array(
            'label' => array('Opacité', 'Ajustez, si souhaité, l\'opacité de l\'image (Valeur entre 0 et 10)'),
            'inputType' => 'text',
            'eval' => array('rgxp' => 'digit', 'tl_class' => 'w50', 'min'=>0, 'max'=>10)
        ),

        'imagesize_legend' => array(
            'label' => array('Image - Positionnement'),
            'inputType' => 'group',
        ),
        'imagesize_horizontal' => array(
            'label' => array('Alignement Horizontal', 'Si souhaité, ajustez la position horizontale de l\'image'),
            'inputType' => 'select',
            'options' => array(
                '' => 'Aucun',
                'left' => 'Gauche',
                'right' => 'Droite',
            ),
            'eval' => array('tl_class'=>'w50 clr'),
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

        'contentstyle_legend' => array(
            'label' => array('Style du contenu'),
            'inputType' => 'group',
        ),
        'content_horizontal' => array(
            'label' => array('Alignement Horizontal', 'Si souhaité, ajustez la position horizontale du contenu'),
            'inputType' => 'select',
            'options' => array(
                'center' => 'Centré',
                'left' => 'Gauche',
                'right' => 'Droite',
            ),
            'eval' => array('tl_class'=>'w50'),
        ),
        'content_vertical' => array(
            'label' => array('Alignement vertical', 'Si souhaité, ajustez la position verticale du contenu'),
            'inputType' => 'select',
            'options' => array(
                'center' => 'Centré',
                'top' => 'Haut',
                'bottom' => 'Bas',
            ),
            'eval' => array('tl_class'=>'w50'),
        ),
        'content_background' => array(
            'label' => array('Fond du texte', 'Si souhaité, ajustez le fond du contenu'),
            'inputType' => 'select',
            'options' => \WEM\SmartGear\Backend\Util::getSmartgearColors(),
            'eval' => array('tl_class'=>'w50 clr'),
        ),
        'content_background_opacity' => array(
            'label' => array('Opacité', 'Ajustez, si souhaité, l\'opacité du fond (Valeur entre 0 et 10)'),
            'inputType' => 'text',
            'eval' => array('rgxp' => 'digit', 'tl_class' => 'w50', 'min'=>0, 'max'=>10)
        ),
    )
);
