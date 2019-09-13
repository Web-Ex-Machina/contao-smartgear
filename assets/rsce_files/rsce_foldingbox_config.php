<?php
// rsce_kwicks_config.php
return array(
    'label' => array('Folding Box', 'Générez un ensemble de panels ouvrants')
    ,'contentCategory' => 'SMARTGEAR'
    ,'standardFields' => array('cssID')
    ,'fields' => array(
        'config_legend' => array(
            'label' => array("Configuration de l'élément")
            ,'inputType' => 'group'
        )
        ,'headline' => array(
            'inputType' => 'standardField'
            ,'eval' => array('tl_class' => 'w50')
        )
        ,'height' => array(
            'label' => array('Hauteur du slider', 'Configurez la hauteur de l\'élément')
            ,'inputType' => 'text'
            ,'eval' => array('tl_class' => 'w50')
        )
        ,'break' => array(
            'label' => array('Responsive', 'Si souhaité, ajustez le moment où les éléments passent les uns en dessous des autres')
            ,'inputType' => 'select'
            ,'options' => array(''=>'Par défaut', 'xxs'=>'XXS / 520px', 'xs'=>'XS / 620px', 'sm'=>'SM / 768px', 'md'=>'MD / 992px', 'lg'=>'LG / 1200px', 'xl'=>'XL / 1400px')
            ,'eval' => array('tl_class'=>'w50')
        )

        // Items
        ,'items' => array(
            'label' => array('Panels', 'Editez les panels')
            ,'elementLabel' => '%s. Panel'
            ,'inputType' => 'list'
            ,'fields' => array(
                // Background
                'img_src' => array(
                    'label' => array('Image de fond', 'Insérez une image qui sera utilisé comme fond de cet item')
                    ,'inputType' => 'fileTree'
                    ,'eval' => array('filesOnly'=>true, 'fieldType'=>'radio', 'extensions'=>Config::get('validImageTypes'))
                )
                ,'img_size' => array(
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['size']
                    ,'inputType' => 'imageSize'
                    ,'reference' => &$GLOBALS['TL_LANG']['MSC']
                    ,'eval'      => array('rgxp'=>'natural', 'includeBlankOption'=>true, 'nospace'=>true, 'helpwizard'=>true, 'tl_class'=>'w50')
                    ,'options_callback' => function () {
                        return System::getContainer()->get('contao.image.image_sizes')->getOptionsForUser(BackendUser::getInstance());
                    }
                )
                ,'img_alt' => array(
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['alt']
                    ,'inputType' => 'text'
                    ,'eval' => array('tl_class'=>'w50')
                )

                // Content
                ,'content' => array(
                    'label' => array('Contenu', 'Saisissez le contenu textuel de l\'élément')
                    ,'inputType' => 'textarea'
                    ,'eval' => array('rte' => 'tinyMCE', 'tl_class' => 'clr')
                )

                // Link
                ,'link_href' => array(
                    'label' => &$GLOBALS['TL_LANG']['MSC']['url']
                    ,'inputType' => 'text'
                    ,'eval' => array('rgxp'=>'url', 'tl_class' => 'w50 wizard')
                    ,'wizard' => array(array('tl_content', 'pagePicker'))
                )
                ,'link_title' => array(
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['linkTitle']
                    ,'inputType' => 'text'
                    ,'eval' => array('tl_class'=>'w50')
                )
                ,'link_text' => array(
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['titleText']
                    ,'inputType' => 'text'
                    ,'eval' => array('tl_class'=>'w50')
                )
                ,'link_target' => array(
                    'label' => &$GLOBALS['TL_LANG']['MSC']['target']
                    ,'inputType' => 'checkbox'
                    ,'eval' => array('tl_class'=>'w50')
                )
            )
        )
    )
);
