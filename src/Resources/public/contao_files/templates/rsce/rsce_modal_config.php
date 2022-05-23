<?php

/**
 * rsce_block-img_config.php
 * https://demo.smartgear.webexmachina.fr/guidelines.html
 */
return array(
    'label' => array('Popup / Modal', 'Générez une fenêtre popup'),
    'contentCategory' => 'SMARTGEAR',
    'standardFields' => array('cssID'),
    'fields' => array(
        'modal_legend' => array(
            'label' => array('Paramètres modal'),
            'inputType' => 'group',
        ),
        'modal_title' => array(
            'label' => array('Titre', 'Titre de la modal, facultatif')
            ,'inputType' => 'text'
            ,'eval' => array('tl_class'=>'w50')
        ),
        'content_legend' => array(
            'label' => array('Contenu'),
            'inputType' => 'group',
        ),
        'content_type' => array(
            'label' => array('Type de contenu', 'type de contenu a utiliser dans la modal'),
            'inputType' => 'select',
            'options' => array(
                'text' => 'Texte',
                'picture' => 'Image',
                'article' => 'Article',
                'form' => 'Formulaire',
                'module' => 'Module',
                'html' => 'HTML',
            ),
            'eval' => array('tl_class'=>'w50'),
        ),
        'text' => array(
            'label' => array('Texte', 'Texte affiché dans la modal'),
            'inputType' => 'standardField',
            'eval' => array('mandatory'=>false, 'tl_class'=>'clr'),
            'dependsOn' => array(
                'field' => 'content_type', 
                'value' => 'text',      
            ),
        ),
        'html' => array(
            'label' => array('HTML', 'Code HTML affiché dans la modal'),
            'inputType' => 'standardField',
            'eval' => array('mandatory'=>false, 'tl_class'=>'clr'),
            'dependsOn' => array(
                'field' => 'content_type', 
                'value' => 'html',      
            ),
        ),
        'article' => array(
            'label' => array('Article','Selectionnez un article à afficher dans la modal'),
            'inputType' => 'standardField',
            'eval' => array('mandatory'=>false, 'tl_class'=>'clr'),
            'dependsOn' => array(
                'field' => 'content_type', 
                'value' => 'article',      
            ),
        ),
        'form' => array(
            'label' => array('Form','Selectionnez un formulaire à afficher dans la modal'),
            'inputType' => 'standardField',
            'eval' => array('mandatory'=>false, 'tl_class'=>'clr'),
            'dependsOn' => array(
                'field' => 'content_type', 
                'value' => 'form',      
            ),
        ),
        'module' => array(
            'label' => array('Module','Selectionnez un module à afficher dans la modal'),
            'inputType' => 'standardField',
            'eval' => array('mandatory'=>false, 'tl_class'=>'clr'),
            'dependsOn' => array(
                'field' => 'content_type', 
                'value' => 'module',      
            ),
        ),
        'singleSRC' => array(
            'label' => array('Image','Selectionnez une image à afficher dans la modal'),
            'inputType' => 'standardField',
            'eval' => array('mandatory'=>false,'extensions'=>Config::get('validImageTypes'), 'tl_class'=>'clr'),
            'dependsOn' => array(
                'field' => 'content_type', 
                'value' => 'picture',      
            ),
        ),
        'size' => array(
            // 'label' => array('Taille de l\'image','Selectionnez un fichier à afficher dans la modal'),
            'inputType' => 'standardField',
            'eval' => array('mandatory'=>false,'includeBlankOption'=>true, 'nospace'=>true, 'tl_class'=>'w50 clr'),
            'dependsOn' => array(
                'field' => 'content_type', 
                'value' => 'picture',      
            ),
        ),
        'alt' => array(
            // 'label' => array('Taille de l\'image','Selectionnez un fichier à afficher dans la modal'),
            'inputType' => 'standardField',
            'eval' => array('mandatory'=>false, 'tl_class'=>'w50 clr'),
            'dependsOn' => array(
                'field' => 'content_type', 
                'value' => 'picture',      
            ),
        ),
        'imageTitle' => array(
            // 'label' => array('Taille de l\'image','Selectionnez un fichier à afficher dans la modal'),
            'inputType' => 'standardField',
            'eval' => array('mandatory'=>false, 'tl_class'=>'w50'),
            'dependsOn' => array(
                'field' => 'content_type', 
                'value' => 'picture',      
            ),
        ),
        'trigger_legend' => array(
            'label' => array('Déclenchement'),
            'inputType' => 'group',
        ),
        'trigger_type' => array(
            'label' => array('Type de déclenchement', 'Sélectionnez la façon dont la modal s\'ouvre'),
            'inputType' => 'select',
            'options' => array(
                'button' => 'Bouton',
                'link'   => 'Lien',
                'onload' => 'Ouverture au chargement de la page',
                'custom' => 'Custom script',
            ),
            'eval' => array('tl_class'=>'w50'),
        ),
        'linkTitle' => array(
            // 'label' => array('Custom script', 'Utilisez ce champ pour programmer un script personnalisé'),
            'inputType' => 'standardField',
            'eval' => array('mandatory'=>false, 'tl_class'=>'clr w50'),
            'dependsOn' => array(
                'field' => 'trigger_type', 
                'value' => array('button','link'),      
            ),
        ),
        'titleText' => array(
            // 'label' => array('Custom script', 'Utilisez ce champ pour programmer un script personnalisé'),
            'inputType' => 'standardField',
            'eval' => array('mandatory'=>false, 'tl_class'=>' w50'),
            'dependsOn' => array(
                'field' => 'trigger_type', 
                'value' => array('button','link'),      
            ),
        ),
        'trigger_css' => array(
            'label' => array('Classe(s) css supplémentaire', 'Si souhaité, ajouter des classes css à l\'élément'),
            'inputType' => 'text',
            'eval' => array('mandatory'=>false, 'tl_class'=>' w50'),
            'dependsOn' => array(
                'field' => 'trigger_type', 
                'value' => array('button','link'),      
            ),
        ),
        'trigger_custom' => array(
            'label' => array('Custom script', 'Utilisez ce champ pour programmer un script personnalisé'),
            'inputType' => 'text',
            'eval' => array('mandatory'=>false, 'class'=>'monospace', 'rte'=>'ace|html','tl_class'=>'clr'),
            'dependsOn' => array(
                'field' => 'trigger_type', 
                'value' => 'custom',      
            ),
        ),
        'advanced_legend' => array(
            'label' => array('Paramètres avancés'),
            'inputType' => 'group',
        ),
        'modal_name' => array(
            'label' => array('Nom', 'Nom de la modal, facultatif. Sert a identifier la modal dans un script personnalisé'),
            'inputType' => 'text',
            'eval' => array('tl_class'=>'w50'),
        ),
        'modal_autoload' => array(
            'label' => array("Préchargement", "Cochez pour charger le contenu de la modal en arrière-plan"),
            'inputType' => 'checkbox',
            'eval' => array( 'tl_class' => 'w50 clr'),
            'default' => true
        ),
        'modal_autodestroy' => array(
            'label' => array("Autodestruction", "Cochez pour détruire la modal lors de sa fermeture"),
            'inputType' => 'checkbox',
            'eval' => array( 'tl_class' => 'w50 clr'),
        ),
        'modal_refresh' => array(
            'label' => array("Bouton rafraichir", "Cochez pour ajouter un bouton permettant de rafraichir le contenu de la modal"),
            'inputType' => 'checkbox',
            'eval' => array( 'tl_class' => 'w50 clr'),
        ),
    ),
);
