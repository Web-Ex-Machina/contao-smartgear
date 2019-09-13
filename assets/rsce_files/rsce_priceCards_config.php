<?php
return array(
    'label' => array('Price cards', 'Générez une liste de price cards'),
    'contentCategory' => 'SMARTGEAR',
    'standardFields' => array('cssID'),
    'fields' => array(
        'nbCols_default' => array(
            'label' => array('Nombre de colonnes', 'Indiquez le nombre de colonnes souhaité (entre 1 et 12)'),
            'inputType' => 'text',
            'eval' => array('rgxp' => 'digit', 'tl_class' => 'w50 clr', 'min'=>1, 'max'=>12, 'mandatory'=>true)
        ),
        'responsive_legend' => array(
            'label' => array('Configuration responsive')
            ,'inputType' => 'group'
        ),
        'nbCols_xl' => array(
            'label' => array('Nombre de colonnes < 1400px', 'Indiquez le nombre de colonnes souhaité (entre 1 et 12)'),
            'inputType' => 'text',
            'eval' => array('rgxp' => 'digit', 'tl_class' => 'w50 clr', 'min'=>1, 'max'=>12)
        ),
        'nbCols_lg' => array(
            'label' => array('Nombre de colonnes < 1200px', 'Indiquez le nombre de colonnes souhaité (entre 1 et 12)'),
            'inputType' => 'text',
            'eval' => array('rgxp' => 'digit', 'tl_class' => 'w50 clr', 'min'=>1, 'max'=>12)
        ),
        'nbCols_md' => array(
            'label' => array('Nombre de colonnes < 992px', 'Indiquez le nombre de colonnes souhaité (entre 1 et 12)'),
            'inputType' => 'text',
            'eval' => array('rgxp' => 'digit', 'tl_class' => 'w50 clr', 'min'=>1, 'max'=>12)
        ),
        'nbCols_sm' => array(
            'label' => array('Nombre de colonnes < 768px', 'Indiquez le nombre de colonnes souhaité (entre 1 et 12)'),
            'inputType' => 'text',
            'eval' => array('rgxp' => 'digit', 'tl_class' => 'w50 clr', 'min'=>1, 'max'=>12)
        ),
        'nbCols_xs' => array(
            'label' => array('Nombre de colonnes < 620px', 'Indiquez le nombre de colonnes souhaité (entre 1 et 12)'),
            'inputType' => 'text',
            'eval' => array('rgxp' => 'digit', 'tl_class' => 'w50 clr', 'min'=>1, 'max'=>12)
        ),
        'nbCols_xxs' => array(
            'label' => array('Nombre de colonnes < 520px', 'Indiquez le nombre de colonnes souhaité (entre 1 et 12)'),
            'inputType' => 'text',
            'eval' => array('rgxp' => 'digit', 'tl_class' => 'w50 clr', 'min'=>1, 'max'=>12)
        ),
        'listItems' => array(
            'label' => array('Price Cards', 'Editez les cards')
            ,'elementLabel' => '%s. price card'
            ,'inputType' => 'list'
            ,'fields' => array(
                'title' => array(
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['headline'],
                    'inputType' => 'text',
                    'eval' => array('mandatory'=>false)
                ),
                'price_legend' => array(
                    'label' => array('Prix')
                    ,'inputType' => 'group'
                ),
                'amount' => array(
                    'label' => array('Prix', 'Saisissez le prix/nombre')
                    ,'inputType' => 'text'
                    ,'eval' => array('tl_class' => 'clr w50')
                ),
                'currency' => array(
                    'label' => array('Devise', 'Saisissez la devise/unité (€,$,£,...)')
                    ,'inputType' => 'text'
                    ,'eval' => array('tl_class' => 'clr w50')
                ),
                'period' => array(
                    'label' => array('Récurrence', 'Saisissez la récurrence (par mois, par jour,...)')
                    ,'inputType' => 'text'
                    ,'eval' => array('tl_class' => 'clr w50')
                ),
                'content_legend' => array(
                    'label' => array('Contenus')
                    ,'inputType' => 'group'
                ),
                'lines' => array(
                    'label' => array('Lignes', 'Ajouter x lignes de contenus (html autorisé)')
                    ,'inputType' => 'listWizard'
                    ,'eval' => array('tl_class' => 'clr w50','allowHtml'=>true)
                ),
                'cta_text' => array(
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['linkTitle']
                    ,'inputType' => 'text'
                    ,'eval' => array('tl_class'=>'w50 clr', 'mandatory' => false)
                ),
                'cta_href' => array(
                    'label' => &$GLOBALS['TL_LANG']['MSC']['url']
                    ,'inputType' => 'text'
                    ,'eval' => array('rgxp'=>'url', 'tl_class' => 'w50 wizard ')
                    ,'wizard' => array(array('tl_content', 'pagePicker'))
                ),
                'cta_title' => array(
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['titleText']
                    ,'inputType' => 'text'
                    ,'eval' => array('tl_class'=>'w50')
                ),
                'cta_target' => array(
                    'label' => &$GLOBALS['TL_LANG']['MSC']['target']
                    ,'inputType' => 'checkbox'
                    ,'eval' => array('tl_class'=>'w50')
                ),
                'cta_classes' => array(
                    'label' => array('Classes supplémentaires', 'Indiquez, si souhaité, la ou les classes css à ajouter au bouton')
                    ,'inputType' => 'text'
                    ,'eval' => array('tl_class'=>'w50 clr')
                ),
                'style_legend' => array(
                    'label' => array('Apparence')
                    ,'inputType' => 'group'
                ),
                'font_color' => array(
                    'label' => array('Couleur du texte', 'Si souhaité, ajustez la couleur du contenu'),
                    'inputType' => 'select',
                    'options' => \WEM\SmartGear\Backend\Util::getSmartgearColors(),
                    'eval' => array('tl_class'=>'w50'),
                ),
                'bg_color' => array(
                    'label' => array('Couleur du fond', 'Si souhaité, ajustez la couleur de fond du bloc'),
                    'inputType' => 'select',
                    'options' => \WEM\SmartGear\Backend\Util::getSmartgearColors(),
                    'eval' => array('tl_class'=>'w50'),
                ),
                'content_color' => array(
                    'label' => array('Couleur du titre et du prix', 'Si souhaité, ajustez la couleur du titre et du prix'),
                    'inputType' => 'select',
                    'options' => \WEM\SmartGear\Backend\Util::getSmartgearColors(),
                    'eval' => array('tl_class'=>'w50'),
                ),
                'isMain' => array(
                    'label' => array('Vedette','Met en valeur le bloc')
                    ,'inputType' => 'checkbox'
                    ,'eval' => array('tl_class'=>'w50')
                ),
            )
        ),
    ),
);
