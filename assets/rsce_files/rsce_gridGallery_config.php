<?php
return array
(
    'label' => array('Galerie responsive', 'Générez une galerie d\'images personnalisée utilisant une grille responsive'),
    'contentCategory' => 'SMARTGEAR',
    'standardFields' => array('cssID'),
    'fields' => array
    (
        'headline' => array
        (
            'inputType' => 'standardField'
            ,'eval' => array('tl_class' => 'w100')
        ),
        'nbCols_default' => array
        (
            'label' => array('Nombre de colonnes', 'Indiquez le nombre de colonnes souhaité (entre 1 et 12)'),
            'inputType' => 'text',
            'eval' => array('rgxp' => 'digit', 'tl_class' => 'w50', 'min'=>1, 'max'=>12, 'mandatory'=>true)
        ),
        'ratio' => array
        (
            'label' => array('Ratio des images', 'Si souhaité, indiquez le ratio par défaut des images de la galerie'),
            'inputType' => 'select',
            'options' => array
            (
                '' => ' - ',
                'r_1-1'  => '1:1',
                'r_2-1'  => '2:1',
                'r_1-2'  => '1:2',
                'r_16-9' => '16:9',
            ),
            'eval' => array('tl_class'=>'w50'),
        ),
        'responsive_legend' => array
        (
            'label' => array('Configuration responsive')
            ,'inputType' => 'group'
        ),
        'nbCols_xl' => array
        (
            'label' => array('Nombre de colonnes < 1400px', 'Indiquez le nombre de colonnes souhaité (entre 1 et 12)'),
            'inputType' => 'text',
            'eval' => array('rgxp' => 'digit', 'tl_class' => 'w50', 'min'=>1, 'max'=>12)
        ),
        'nbCols_lg' => array
        (
            'label' => array('Nombre de colonnes < 1200px', 'Indiquez le nombre de colonnes souhaité (entre 1 et 12)'),
            'inputType' => 'text',
            'eval' => array('rgxp' => 'digit', 'tl_class' => 'w50', 'min'=>1, 'max'=>12)
        ),
        'nbCols_md' => array
        (
            'label' => array('Nombre de colonnes < 992px', 'Indiquez le nombre de colonnes souhaité (entre 1 et 12)'),
            'inputType' => 'text',
            'eval' => array('rgxp' => 'digit', 'tl_class' => 'w50', 'min'=>1, 'max'=>12)
        ),
        'nbCols_sm' => array
        (
            'label' => array('Nombre de colonnes < 768px', 'Indiquez le nombre de colonnes souhaité (entre 1 et 12)'),
            'inputType' => 'text',
            'eval' => array('rgxp' => 'digit', 'tl_class' => 'w50', 'min'=>1, 'max'=>12)
        ),
        'nbCols_xs' => array
        (
            'label' => array('Nombre de colonnes < 620px', 'Indiquez le nombre de colonnes souhaité (entre 1 et 12)'),
            'inputType' => 'text',
            'eval' => array('rgxp' => 'digit', 'tl_class' => 'w50', 'min'=>1, 'max'=>12)
        ),
        'nbCols_xxs' => array
        (
            'label' => array('Nombre de colonnes < 520px', 'Indiquez le nombre de colonnes souhaité (entre 1 et 12)'),
            'inputType' => 'text',
            'eval' => array('rgxp' => 'digit', 'tl_class' => 'w50', 'min'=>1, 'max'=>12)
        ),
        'listItems' => array
        (
            'label' => array('Images', 'Editez les images')
            ,'elementLabel' => '%s. image'
            ,'inputType' => 'list'
            ,'fields' => array
            (
                'img_legend' => array
                (
                    'label' => array('Configuration de l\'image')
                    ,'inputType' => 'group'
                ),
                'img_src' => array
                (
                    'label' => array('Image', 'Sélectionnez une image')
                    ,'inputType' => 'fileTree'
                    ,'eval' => array('filesOnly'=>true, 'fieldType'=>'radio', 'extensions'=>Config::get('validImageTypes'),'mandatory'=>true)
                ),
                'img_size' => array
                (
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['size']
                    ,'inputType' => 'imageSize'
                    ,'reference' => &$GLOBALS['TL_LANG']['MSC']
                    ,'eval'      => array('rgxp'=>'natural', 'includeBlankOption'=>true, 'nospace'=>true, 'helpwizard'=>true, 'tl_class'=>'w50')
                    ,'options_callback' => function ()
                    {
                        return System::getContainer()->get('contao.image.image_sizes')->getOptionsForUser(BackendUser::getInstance());
                    }
                ),
                'img_alt' => array
                (
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['alt']
                    ,'inputType' => 'text'
                    ,'eval' => array('tl_class'=>'w50')
                ),
                'ratio' => array
                (
                    'label' => array('Ratio de l\'image', 'Si souhaité, indiquez le ratio de cette image'),
                    'inputType' => 'select',
                    'options' => array
                    (
                        '' => ' - ',
                        'r_1-1'  => '1:1',
                        'r_2-1'  => '2:1',
                        'r_1-2'  => '1:2',
                        'r_16-9' => '16:9',
                    ),
                    'eval' => array('tl_class'=>'w50 clr'),
                ),
                'link_legend' => array
                (
                    'label' => array('Configuration du lien')
                    ,'inputType' => 'group'
                ),
                'href' => array
                (
                    'label' => &$GLOBALS['TL_LANG']['MSC']['url']
                    ,'inputType' => 'text'
                    ,'eval' => array('rgxp'=>'url', 'tl_class' => 'w50 wizard ')
                    ,'wizard' => array(array('tl_content', 'pagePicker'))
                ),
                'title' => array
                (
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['titleText']
                    ,'inputType' => 'text'
                    ,'eval' => array('tl_class'=>'w50')
                ),
                'target' => array
                (
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['fullsize']
                    ,'inputType' => 'checkbox'
                    ,'eval' => array('tl_class'=>'w50')
                ),
                'misc_legend' => array
                (
                    'label' => array('Configuration avancée')
                    ,'inputType' => 'group'
                ),
                'span_cols' => array
                (
                    'label' => array('Empiétement - colonnes', 'Indiquez le nombre de colonnes sur lequel doit s\'étendre l\'image'),
                    'inputType' => 'text',
                    'eval' => array('rgxp' => 'digit', 'tl_class' => 'w50', 'min'=>1, 'max'=>12)
                ),
                'span_rows' => array
                (
                    'label' => array('Empiétement - lignes', 'Indiquez le nombre de lignes sur lequel doit s\'étendre l\'image'),
                    'inputType' => 'text',
                    'eval' => array('rgxp' => 'digit', 'tl_class' => 'w50', 'min'=>1, 'max'=>12)
                ),
                'classes' => array
                (
                    'label' => array('Classes supplémentaires', 'Indiquez, si souhaité, la ou les classes css à ajouter a l\'image')
                    ,'inputType' => 'text'
                    ,'eval' => array('tl_class'=>'w50 clr')
                )
            )
        ),
    ),
);