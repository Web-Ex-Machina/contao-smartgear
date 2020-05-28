<?php
return array(
    'label' => array('Liste icones', 'Générez une liste d\'icones accompagnées de texte.'),
    'types' => array('content'),
    'contentCategory' => 'texts',
    'standardFields' => array('cssID'),
    'fields' => array(
        'listItems' => array(
            'label' => array('Vignettes', 'Editez les vignettes')
            ,'elementLabel' => '%s. vignette'
            ,'inputType' => 'list'
            ,'fields' => array(
                'img_src' => array(
                    'label' => array('Image', 'Sélectionnez une icone')
                    ,'inputType' => 'fileTree'
                    ,'eval' => array('filesOnly'=>true, 'fieldType'=>'radio', 'extensions'=>Config::get('validImageTypes'),'tl_class'=>'w50')
                ),
                'img_text' => array(
                    'label' => array('Icone font-awesome (désactive l\'image sélectionnée)', 'Indiquez le code html de l\'icone désirée (exemple: &lt;i class="fas fa-paper-plane"&gt;&lt;/i&gt;) voir site <a href="https://fontawesome.com/icons?d=gallery" target="_blank">Font Awesome =></a>')
                    ,'inputType' => 'text'
                    ,'eval' => array('tl_class'=>'clr','allowHtml'=>true)
                ),
                'text' => array(
                    'label' => array('Texte', 'Saisissez le texte affiché en dessous de l\'icone')
                    ,'inputType' => 'textarea'
                    ,'eval' => array('rte' => 'tinyMCE', 'tl_class' => 'clr','mandatory'=>true)
                ),
                'href' => array(
                    'label' => &$GLOBALS['TL_LANG']['MSC']['url']
                    ,'inputType' => 'text'
                    ,'eval' => array('rgxp'=>'url', 'tl_class' => 'w50 wizard clr')
                    ,'wizard' => array(array('tl_content', 'pagePicker'))
                ),
                'title' => array(
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['titleText']
                    ,'inputType' => 'text'
                    ,'eval' => array('tl_class'=>'w50')
                ),
                'target' => array(
                    'label' => &$GLOBALS['TL_LANG']['MSC']['target']
                    ,'inputType' => 'checkbox'
                    ,'eval' => array('tl_class'=>'w50')
                ),
                'classes' => array(
                    'label' => array('Classes supplémentaires', 'Indiquez, si souhaité, la ou les classes css à ajouter à l\'item')
                    ,'inputType' => 'text'
                    ,'eval' => array('tl_class'=>'w50 clr')
                )
            )
        ),
    ),
);
