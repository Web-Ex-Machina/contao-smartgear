<?php
// rsce_counterFW_config.php
return array(
    'label' => array('Compteur', 'Générez un compteur animé')
    ,'contentCategory' => 'SMARTGEAR'
    ,'standardFields' => array('cssID')
    ,'fields' => array(
        'config_legend' => array(
            'label' => array('Configuration du compteur')
            ,'inputType' => 'group'
        )
        ,'startVal' => array(
            'label' => array('Valeur de départ', 'Définissez la valeur de départ du compteur')
            ,'inputType' => 'text'
            ,'eval' => array('tl_class' => 'w50', 'rgxp' => 'digit','mandatory' => true)
        )
        ,'endVal' => array(
            'label' => array('Valeur de fin', 'Définissez la valeur de fin du compteur')
            ,'inputType' => 'text'
            ,'eval' => array('tl_class' => 'w50', 'rgxp' => 'digit','mandatory' => true)
        )
        ,'decimals' => array(
            'label' => array('Nombre de décimales', 'Définissez le nombre de décimales du compteur (0 par défaut)')
            ,'inputType' => 'text'
            ,'eval' => array('tl_class' => 'w50', 'rgxp' => 'digit', 'minval' => 0)
        )
        ,'duration' => array(
            'label' => array('Durée', 'Définissez durée d\'animation en seconde du compteur (2 par défaut)')
            ,'inputType' => 'text'
            ,'eval' => array('tl_class' => 'w50', 'rgxp' => 'digit', 'minval' => 0)
        )
        ,'delay' => array(
            'label' => array('Délai', 'Définissez un temps d\'attente en seconde avant déclenchement de l\'animation du compteur (0 par défaut)')
            ,'inputType' => 'text'
            ,'eval' => array('tl_class' => 'w50', 'rgxp' => 'digit', 'minval' => 0)
        )
        ,'unit' => array(
            'label' => array('Unité', 'Définissez une unité qui sera affichée à côté du compteur (exemple: m²)')
            ,'inputType' => 'text'
            ,'eval' => array('tl_class' => 'w50')
        )
        ,'label' => array(
            'label' => array('Label', 'Définissez un court texte qui sera affiché en dessous du compteur')
            ,'inputType' => 'text'
            ,'eval' => array('tl_class' => 'w50')
        )
    )
);
