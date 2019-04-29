<?php
// rsce_testimonials_config.php
return array
(
    'label' => array('Testimonials', 'Générez un slider affichant des témoignages/citations')
    ,'contentCategory' => 'SMARTGEAR'
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
        ,'slide_autoplay' => array
        (
            'label' => array("Démarrage automatique", "Cochez pour que les contenus défilent automatiquement")
            ,'inputType' => 'checkbox'
            ,'eval' => array('tl_class'=>'w50 clr')
        )
        ,'slider_loop' => array
        (
            'label' => array("Loop", "Cochez pour visualiser les contenus en boucle")
            ,'inputType' => 'checkbox'
            ,'eval' => array('tl_class'=>'w50')
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
            'label' => array('Position horizontale', 'Si souhaité, ajustez la position horizontale du contenu')
            ,'inputType' => 'select'
            ,'options' => array(''=>'Aucun', 'left' => 'A gauche', 'right' => 'A droite')
            ,'eval' => array('tl_class'=>'w50')
        )
        ,'nav_vertical' => array
        (
            'label' => array('Position verticale', 'Si souhaité, ajustez la position verticale du contenu')
            ,'inputType' => 'select'
            ,'options' => array(''=>'Aucun', 'top'=>'En Haut', 'bottom'=>'En Bas')
            ,'eval' => array('tl_class'=>'w50')
        )
        ,'nav_arrows' => array
        (
            'label' => array("Navigation fléchée", "Cochez pour remplacer la navigation par des flèches latérales")
            ,'inputType' => 'checkbox'
            ,'eval' => array('tl_class'=>'w50 clr')
        )

        // Items
        ,'items' => array
        (
            'label' => array('Témoignages/citations', 'Editez les témoignages/citations')
            ,'elementLabel' => '%s. témoignage/citation'
            ,'inputType' => 'list'
            ,'fields' => array
            (
                // Background
                'slide_img_src' => array
                (
                    'label' => array('Fond', 'Insérez une image qui sera utilisé comme fond de cet élément')
                    ,'inputType' => 'fileTree'
                    ,'eval' => array('filesOnly'=>true, 'fieldType'=>'radio', 'extensions'=>Config::get('validImageTypes'))
                )
                ,'slide_img_size' => array
                (
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['size']
                    ,'inputType' => 'imageSize'
                    ,'reference' => &$GLOBALS['TL_LANG']['MSC']
                    ,'eval'      => array('rgxp'=>'natural', 'includeBlankOption'=>true, 'nospace'=>true, 'helpwizard'=>true, 'tl_class'=>'w50 clr')
                    ,'options_callback' => function ()
                    {
                        return System::getContainer()->get('contao.image.image_sizes')->getOptionsForUser(BackendUser::getInstance());
                    }
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
                    'label' => array('Témoignage/citation', 'Saisissez le texte de cet élément')
                    ,'inputType' => 'textarea'
                    ,'eval' => array('rte' => 'tinyMCE', 'tl_class' => 'clr')
                )
                ,'slide_author' => array
                (
                    'label' => array('Auteur', 'Saisissez l\'auteur de cet élément')
                    ,'inputType' => 'text'
                    ,'eval' => array('tl_class'=>'w50','tl_class' => 'clr')
                )
                ,'author_classes' => array
                (
                    'label' => array('Classes supplémentaires auteur', 'Indiquez, si souhaité, la ou les classes css à ajouter au bloc de l\'auteur')
                    ,'inputType' => 'text'
                    ,'eval' => array('tl_class'=>'w50 clr')
                )
            )
        )
    )
);