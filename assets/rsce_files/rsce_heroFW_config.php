<?php
// rsce_kwicks_config.php
return array
(
    'label' => array('Hero FW', 'Générez un élément stylisé composé d\'un texte sur une image')
    ,'types' => array('content')
    ,'contentCategory' => 'includes'
    ,'standardFields' => array('cssID')
    ,'fields' => array
    (
        'config_legend' => array
        (
            'label' => array('Configuration du bloc')
            ,'inputType' => 'group'
        )
        ,'force_fullwidth' => array
        (
            'label' => array("Toute la largeur", "Cochez pour forcer le bloc à prendre toute la largeur de l'écran")
            ,'inputType' => 'checkbox'
        ),

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
            'eval' => array('tl_class'=>'w50')
        ),
        'image_opacity' => array
        (
            'label' => array('Opacité', 'Ajustez, si souhaité, l\'opacité de l\'image (Valeur entre 0 et 10)'),
            'inputType' => 'text',
            'eval' => array('rgxp' => 'digit', 'tl_class' => 'w50', 'min'=>0, 'max'=>10)
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
            'eval' => array('tl_class'=>'w50 clr'),
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

        'content_legend' => array
        (
            'label' => array('Contenu'),
            'inputType' => 'group',
        ),   
        'headline' => array
        (
            'inputType' => 'standardField',
            'eval' => array('mandatory'=>false)
        ),
        'text' => array
        (
            'inputType' => 'standardField',
            'eval' => array('mandatory'=>false, 'tl_class'=>'clr')
        ),
        'url' => array
        (
            'inputType' => 'standardField',
            'eval' => array('mandatory'=>false)
        ),
        'linkTitle' => array
        (
            'inputType' => 'standardField',
        ),

        'contentstyle_legend' => array
        (
            'label' => array('Style du contenu'),
            'inputType' => 'group',
        ),
        'content_horizontal' => array
        (
            'label' => array('Alignement Horizontal', 'Si souhaité, ajustez la position horizontale du contenu'),
            'inputType' => 'select',
            'options' => array
            (
                'center' => 'Centré',
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
                'center' => 'Centré',
                'top' => 'Haut',
                'bottom' => 'Bas',
            ),
            'eval' => array('tl_class'=>'w50'),
        ),
        'content_color' => array
        (
            'label' => array('Couleur du texte', 'Si souhaité, ajustez la couleur du contenu'),
            'inputType' => 'select',
            'options' => array
            (
                ""=>"Par défaut"
                ,"ft-blue"=>"blue"
                ,"ft-green"=>"green"
                ,"ft-orange"=>"orange"
                ,"ft-darkblue"=>"darkblue"
                ,"ft-gold"=>"gold"
                ,"ft-black"=>"black"
                ,"ft-blacklight"=>"blacklight"
                ,"ft-blacklighter"=>"blacklighter"
                ,"ft-greystronger"=>"greystronger"
                ,"ft-greystrong"=>"greystrong"
                ,"ft-grey"=>"grey"
                ,"ft-greylight"=>"greylight"
                ,"ft-greylighter"=>"greylighter"
                ,"ft-white"=>"white"
                ,"ft-none"=>"none"
            ),
            'eval' => array('tl_class'=>'w50'),
        ),
        'content_background' => array
        (
            'label' => array('Fond du texte', 'Si souhaité, ajustez le fond du contenu'),
            'inputType' => 'select',
            'options' => array
            (
                ""=>"Par défaut"
                ,"ft-blue"=>"blue"
                ,"ft-green"=>"green"
                ,"ft-orange"=>"orange"
                ,"ft-darkblue"=>"darkblue"
                ,"ft-gold"=>"gold"
                ,"ft-black"=>"black"
                ,"ft-blacklight"=>"blacklight"
                ,"ft-blacklighter"=>"blacklighter"
                ,"ft-greystronger"=>"greystronger"
                ,"ft-greystrong"=>"greystrong"
                ,"ft-grey"=>"grey"
                ,"ft-greylight"=>"greylight"
                ,"ft-greylighter"=>"greylighter"
                ,"ft-white"=>"white"
                ,"ft-none"=>"none"
            ),
            'eval' => array('tl_class'=>'w50 clr'),
        ),
        'content_background_opacity' => array
        (
            'label' => array('Opacité', 'Ajustez, si souhaité, l\'opacité du fond (Valeur entre 0 et 10)'),
            'inputType' => 'text',
            'eval' => array('rgxp' => 'digit', 'tl_class' => 'w50', 'min'=>0, 'max'=>10)
        ),
    )
);