<?php
// rsce_timeline_config.php
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
    'label' => array('Timeline', 'Générez une timeline'),
    'types' => array('content'),
    'contentCategory' => 'includes',
    'standardFields' => array('cssID'),
    'fields' => array(
        'headline' => array(
            'inputType' => 'standardField'
            ,'eval' => array('tl_class' => 'w50','allowHtml'=>true)
        ),
        'timeline_style' => array(
            'label' => array('Style', 'Choisissez un style à appliquer à la timeline'),
            'inputType' => 'select',
            'options' => array(
                '' => 'Par défaut',
                'condensed' => 'Condensé',
            ),
            'eval' => array('tl_class'=>'w50 clr'),
        ),
        'timeline_color' => array(
            'label' => array('Couleur timeline', 'Change la couleur de la timeline'),
            'inputType' => 'select',
            'options' => $arrColors,
            'eval' => array('tl_class'=>'w50 clr'),
        ),
        'title_color' => array(
            'label' => array('Couleur titres', 'Change la couleur des titres et années'),
            'inputType' => 'select',
            'options' => $arrColors,
            'eval' => array('tl_class'=>'w50'),
        ),
        'image_legend' => array(
            'label' => array('Image'),
            'inputType' => 'group',
        ),
        'image' => array(
            'label' => array('Image', 'Insérez une image qui sera utilisée pour illustrer votre timeline'),
            'inputType' => 'fileTree',
            'eval' => array('filesOnly'=>true, 'fieldType'=>'radio', 'extensions'=>Config::get('validImageTypes')),
        ),
        'imageCrop' => array(
            'label' => array('Redimensionnement', 'Ajustez si souhaité votre illustration'),
            'inputType' => 'imageSize',
            'options' => System::getImageSizes(),
            'reference' => &$GLOBALS['TL_LANG']['MSC'],
            'eval' => array('rgxp'=>'natural', 'includeBlankOption'=>true, 'nospace'=>true, 'helpwizard'=>true, 'tl_class'=>'w50'),
        ),
        'imagePos' => array(
            'label' => array('Position', 'Sélectionnez la position de votre illustration, à droite ou à gauche de la timeline'),
            'inputType' => 'select',
            'options' => array('left'=>'Gauche','right'=>'Droite'),
            'eval' => array('tl_class' => ' w50')
        ),
        // Items
        'items' => array(
            'label' => array('Items', 'Editez les items de votre Timeline'),
            'elementLabel' => '%s. item',
            'inputType' => 'list',
            'fields' => array(
                // year, icon, title , texte
                // Content
                'year' => array(
                    'label' => array('Année', 'Saisissez l\'année a afficher'),
                    'inputType' => 'text',
                    'eval' => array('tl_class' => 'w50','mandatory'=>false),
                ),
                'headline' => array(
                    'label' => array('Titre', 'Saisissez, si souhaité, un titre pour cette date'),
                    'inputType' => 'inputUnit',
                    'options' => array('h2', 'h3', 'h4', 'h5', 'h6'),
                    'eval' => array('tl_class' => 'w100 long m12 clr','allowHtml'=>true)
                ),
                'text' => array(
                    'label' => array('Contenu', 'Saisissez le texte a afficher pour cette date'),
                    'inputType' => 'textarea',
                    'eval' => array('rte' => 'tinyMCE', 'tl_class' => 'clr','mandatory'=>false),
                ),
            ),
        ),
    ),
);
