<?php
// rsce_kwicks_config.php
return array
(
    'label' => array('Slider FW', 'Générez un slider et configurez vos images')
    ,'types' => array('content')
    ,'contentCategory' => 'includes'
    ,'standardFields' => array('cssID')
    ,'fields' => array
    (
        'config_legend' => array
        (
            'label' => array('Configuration du slider')
            ,'inputType' => 'group'
        )
        ,'slide_height' => array
        (
            'label' => array('Hauteur du slider', 'Configurez la hauteur du slider')
            ,'inputType' => 'text'
            ,'eval' => array('tl_class' => 'w50')
        )
        ,'force_fullwidth' => array
        (
            'label' => array("Toute la largeur", "Cochez pour forcer le bloc à prendre toute la largeur de l'écran"),
            'inputType' => 'checkbox',
            'eval' => array( 'tl_class' => 'clr w50')
        )
        ,'slide_autoplay' => array
        (
            'label' => array("Démarrage automatique", "Cochez pour faire en sorte que le slider se lance automatiquement")
            ,'inputType' => 'checkbox'
            ,'eval' => array('tl_class'=>'w50 clr')
        )
        ,'slider_loop' => array
        (
            'label' => array("Répétition", "Cochez pour faire en sorte que le slider se relance une fois fini")
            ,'inputType' => 'checkbox'
            ,'eval' => array('tl_class'=>'w50 clr')
        )
        ,'slider_transition' => array
        (
            'label' => array('Transition', 'Sélectionner le type de transition souhaitée entre les slides')
            ,'inputType' => 'select'
            ,'options' => array('translate'=>'Translation', 'fade'=>'Fading', 'none'=>'Aucune')
            ,'eval' => array('tl_class'=>'w50 clr')
        )

        ,'config_nav_legend' => array
        (
            'label' => array('Configuration de la navigation')
            ,'inputType' => 'group',
        )
        ,'nav_display' => array
        (
            'label' => array('Affichage de la navigation', 'Si souhaité, ajustez la position des boutons de navigation du slider')
            ,'inputType' => 'select'
            ,'options' => array(''=>'Après', 'inner'=>'A l\'intérieur', 'hidden'=>'Caché')
            ,'eval' => array('tl_class'=>'w50')
        )
        ,'nav_horizontal' => array
        (
            'label' => array('Position horizontale', 'Si souhaité, ajustez la position horizontale de la navigation')
            ,'inputType' => 'select'
            ,'options' => array(''=>'Aucun', 'left' => 'A gauche', 'right' => 'A droite')
            ,'eval' => array('tl_class'=>'clr w50')
        )
        ,'nav_vertical' => array
        (
            'label' => array('Position verticale', 'Si souhaité, ajustez la position verticale de la navigation')
            ,'inputType' => 'select'
            ,'options' => array(''=>'Aucun', 'top'=>'En Haut', 'bottom'=>'En Bas')
            ,'eval' => array('tl_class'=>'w50')
        )
        ,'nav_arrows' => array
        (
            'label' => array("Navigation fléchée", "Cochez pour activer la navigation fléchée (désactive la navigation classique)"),
            'inputType' => 'checkbox',
            'eval' => array( 'tl_class' => 'w50 clr')
        )
        ,'disable_swipe' => array
        (
            'label' => array("Désactiver swipe", "Cochez pour désactiver la navigation par swipe"),
            'inputType' => 'checkbox',
            'eval' => array( 'tl_class' => 'w50 clr')
        )

        ,'config_content_legend' => array
        (
            'label' => array('Configuration des contenus')
            ,'inputType' => 'group'
        )
        ,'content_horizontal' => array
        (
            'label' => array('Alignement Horizontal', 'Si souhaité, ajustez la position horizontale du contenu')
            ,'inputType' => 'select'
            ,'options' => array(''=>'Aucun', 'right'=>'Droite','center'=>'Center')
            ,'eval' => array('tl_class'=>'w50')
        )
        ,'content_vertical' => array
        (
            'label' => array('Alignement vertical', 'Si souhaité, ajustez la position verticale du contenu')
            ,'inputType' => 'select'
            ,'options' => array(''=>'Aucun', 'top'=>'Haut', 'center'=>'Centre')
            ,'eval' => array('tl_class'=>'w50')
        )
        ,'content_noblur' => array
        (
            'label' => array("Pas de flou", "Cochez pour désactiver l'effet de flou derrière le contenu")
            ,'inputType' => 'checkbox'
            ,'eval' => array('tl_class'=>'w50')
        )

        // Items
        ,'items' => array
        (
            'label' => array('Slides', 'Editez les slides')
            ,'elementLabel' => '%s. slide'
            ,'inputType' => 'list'
            ,'fields' => array
            (
                // Background
                'slide_img_src' => array
                (
                    'label' => array('Fond', 'Insérez une image qui sera utilisé comme fond de cet item')
                    ,'inputType' => 'fileTree'
                    ,'eval' => array('filesOnly'=>true, 'fieldType'=>'radio', 'extensions'=>Config::get('validImageTypes'))
                )
                ,'slide_img_alt' => array
                (
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['alt']
                    ,'inputType' => 'text'
                    ,'eval' => array('tl_class'=>'w50')
                )

                // Content
                ,'slide_content' => array
                (
                    'label' => array('Contenu', 'Saisissez le contenu textuel de la slide')
                    ,'inputType' => 'textarea'
                    ,'eval' => array('rte' => 'tinyMCE', 'tl_class' => 'clr')
                )

                // Link
                ,'slide_link_href' => array
                (
                    'label' => &$GLOBALS['TL_LANG']['MSC']['url']
                    ,'inputType' => 'text'
                    ,'eval' => array('rgxp'=>'url', 'tl_class' => 'w50 wizard')
                    ,'wizard' => array(array('tl_content', 'pagePicker'))
                )
                ,'slide_link_text' => array
                (
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['linkTitle']
                    ,'inputType' => 'text'
                    ,'eval' => array('tl_class'=>'w50')
                )
                ,'slide_link_title' => array
                (
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['titleText']
                    ,'inputType' => 'text'
                    ,'eval' => array('tl_class'=>'w50')
                )
                ,'slide_link_classes' => array
                (
                    'label' => array('Classe(s) css du lien', 'Saisissez une ou plusieurs classes css à appliquer au lien (séparées par un espace)')
                    ,'inputType' => 'text'
                    ,'eval' => array('tl_class'=>'w50')
                )
                ,'slide_link_target' => array
                (
                    'label' => &$GLOBALS['TL_LANG']['MSC']['target']
                    ,'inputType' => 'checkbox'
                    ,'eval' => array('tl_class'=>'w50')
                )
            )
        )
    )
);