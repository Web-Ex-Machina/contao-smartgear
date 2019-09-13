<?php
$arrColors = array(
                ""=>"Par défaut"
                ,'red'=> "red"
                ,'grey'=> "grey"
                ,'yellow'=> "yellow"
                ,'blue'=> "blue"
                ,'green'=> "green"
                ,'orange'=> "orange"
                ,'darkblue'=> "darkblue"
                ,'gold'=> "gold"
                ,'black'=> "black"
                ,'blacklight'=> "blacklight"
                ,'blacklighter'=> "blacklighter"
                ,'greystronger'=> "greystronger"
                ,'greystrong'=> "greystrong"
                ,'greylight'=> "greylight"
                ,'greylighter'=> "greylighter"
                ,'white'=> "white"
                ,'none'=> "none"
            );

return array(
    'label' => array('Citation', 'Générez un block citation, avec ou sans photo'),
    'types' => array('content'),
    'contentCategory' => 'texts',
    'standardFields' => array('cssID'),
    'fields' => array(
        'image_legend' => array(
            'label' => array('Image'),
            'inputType' => 'group',
        ),
        'singleSRC' => array(
            'inputType' => 'standardField',
            'eval' => array('mandatory'=>false)
        ),
        'alt' => array(
            'inputType' => 'standardField',
            'eval' => array('tl_class'=>'w100 long')
        ),
        'size' => array(
            'inputType' => 'standardField',
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
                'r_4-3' => '4:3',
            ),
            'eval' => array('tl_class'=>'w50'),
        ),
        'image_pos' => array(
            'label' => array('Positionnement', 'Sélectionnez la position de l\'image (gauche ou droite de la citation)'),
            'inputType' => 'select',
            'options' => array(
                'before' => 'Gauche',
                'after' => 'Droite',
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
        'content_legend' => array(
            'label' => array('Contenu'),
            'inputType' => 'group',
        ),
        'text' => array(
            'inputType' => 'standardField',
            'eval' => array('mandatory'=>false, 'tl_class'=>'clr')
        ),
        'author' => array(
            'label' => array('Auteur', 'Si souhaité, indiquez l\'auteur de la citation')
            ,'inputType' => 'text'
            ,'eval' => array('tl_class'=>'w50 clr', 'mandatory' => false)
        ),
        'bg_color' => array(
            'label' => array('Couleur du fond', 'Si souhaité, ajustez la couleur de fond du bloc'),
            'inputType' => 'select',
            'options' => $arrColors,
            'eval' => array('tl_class'=>'w50'),
        ),
    ),
);
