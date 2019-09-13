<?php
// rsce_kwicks_config.php
return array(
    'label' => array('Onglets / Tabs', 'Générez un ensemble d\'onglets ouvrants')
    ,'contentCategory' => 'SMARTGEAR'
    ,'standardFields' => array('cssID')
    ,'fields' => array(
        // Items
        'items' => array(
            'label' => array('Tabs', 'Editez les onglets')
            ,'elementLabel' => '%s. Onglet'
            ,'inputType' => 'list'
            ,'fields' => array(
                // Content
                'title' => array(
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['headline']
                    ,'inputType' => 'text'
                    ,'eval' => array('tl_class'=>'w100 long')
                )
                ,'content' => array(
                    'label' => array('Contenu', 'Saisissez le contenu textuel de la slide')
                    ,'inputType' => 'textarea'
                    ,'eval' => array('rte' => 'tinyMCE', 'tl_class' => 'clr')
                )
            )
        )
    )
);
