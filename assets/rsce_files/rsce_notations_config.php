<?php
return array
(
    'label' => array('Notes', 'Générez une liste notations sous forme d\'étoiles'),
    'types' => array('content'),
    'contentCategory' => 'texts',
    'standardFields' => array('cssID','headline'),
    'fields' => array
    (
        'noteMax' => array
        (
            'label' => array('Note maximale', 'Définissez la note maximale possible.'),
            'inputType' => 'text',
            'default' => 5,
            'eval' => array('tl_class'=>'w50 clr','rgxp' => 'digit'),
        ),
        'notations' => array
        (
            'label' => array('Notes', 'Editez les notes'),
            'elementLabel' => '%s. note',
            'inputType' => 'list',
            'fields' => array
            (
                'label' => array
                (
                    'label' => array('Label', 'Saisissez le label de la note'),
                    'inputType' => 'text',
                    'eval' => array('tl_class' => 'clr w50', 'mandatory' => true)
                ),
                'note' => array
                (
                    'label' => array('Note', 'Saisissez la note'),
                    'inputType' => 'text',
                    'eval' => array('tl_class'=>'w50','rgxp' => 'digit', 'mandatory' => true),
                ),
            )
        ),
        'text' => array
        (
            'label' => array('Texte supplémentaire', 'Saisissez un court texte placé après les notes')
            ,'inputType' => 'textarea'
            ,'eval' => array('rte' => 'tinyMCE', 'tl_class' => 'clr')
        ),
    ),
);