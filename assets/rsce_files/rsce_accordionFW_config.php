<?php
// rsce_accordionFW_config.php
return array
(
    'label' => array('Accordion FW', 'Générez un accordéon et configurez les éléments')
    ,'contentCategory' => 'SMARTGEAR'
    ,'standardFields' => array('cssID')
    ,'fields' => array
    (
        'config_legend' => array
        (
            'label' => array('Configuration de l\'accordéon')
            ,'inputType' => 'group'
        )
        ,'deploy_all' => array
        (
            'label' => array("Tout déployer", "Cochez pour déployer automatiquement tous les éléments")
            ,'inputType' => 'checkbox'
            ,'eval' => array('tl_class'=>'w50 clr')
        )
        ,'disable_collapse' => array
        (
            'label' => array("Désactiver déployer/replier", "Cochez pour désactiver les actions déployer/replier (déploie automatiquement tous les éléments)")
            ,'inputType' => 'checkbox'
            ,'eval' => array('tl_class'=>'w50')
        )
        ,'auto_collapse' => array
        (
            'label' => array("Auto repliage", "Cochez pour replier automatiquement les éléments lors du déploiement de l'un d'eux")
            ,'inputType' => 'checkbox'
            ,'eval' => array('tl_class'=>'w50')
        )

        // Items
        ,'items' => array
        (
            'label' => array('Eléments', 'Editez les éléments')
            ,'elementLabel' => '%s. élément'
            ,'inputType' => 'list'
            ,'fields' => array
            (
                // Content
                'title' => array
                (
                    'label' => &$GLOBALS['TL_LANG']['tl_content']['headline']
                    ,'inputType' => 'text'
                    ,'eval' => array('tl_class'=>'w50','mandatory'=>true)
                )
                ,'hl_title' => array
                (
                    'label' => array('Format titre', 'Selectionner un format de titre a appliquer'),
                    'inputType' => 'select',
                    'options' => array
                    (
                        '' => 'Aucun',
                        'h1' => 'H1',
                        'h2' => 'H2',
                        'h3' => 'H3',
                        'h4' => 'H4',
                        'h5' => 'H5',
                        'h6' => 'H6',
                    ),
                    'eval' => array('tl_class'=>'w50 clr', 'mandatory'=>false),
                )
                ,'content' => array
                (
                    'label' => array('Contenu', 'Saisissez le contenu textuel de l\'élément')
                    ,'inputType' => 'textarea'
                    ,'eval' => array('rte' => 'tinyMCE', 'tl_class' => 'clr','mandatory'=>true)
                )
                ,'lock' => array
                (
                    'label' => array("Lock", "Cochez pour que l'élément soit toujours visible (désactive les actions déployer/replier)")
                    ,'inputType' => 'checkbox'
                    ,'eval' => array('tl_class'=>'w50')
                )
                ,'active' => array
                (
                    'label' => array("Active", "Cochez pour que l'élément soit déployé automatiquement au chargement de la page")
                    ,'inputType' => 'checkbox'
                    ,'eval' => array('tl_class'=>'w50')
                )
            )
        )
    )
);