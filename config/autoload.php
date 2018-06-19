<?php

/**
 * SMARTGEAR for Contao Open Source CMS
 *
 * Copyright (c) 2015-2018 Web ex Machina
 *
 * @author Web ex Machina <https://www.webexmachina.fr>
 */

/**
 * Register the templates
 */
TemplateLoader::addFiles([
    
    // RSCE Templates
    'rsce_block-img'	=> 'templates/rsce'
    ,'rsce_sliderFW'	=> 'templates/rsce'
    ,'rsce_foldingbox'	=> 'templates/rsce'
    ,'rsce_tabs'		=> 'templates/rsce'
    ,'rsce_heroFW'		=> 'templates/rsce'

    // Backend Templates
    ,'be_wem_sg_install' => 'system/modules/wem-contao-smartgear/templates/backend'
    ,'be_wem_sg_module'  => 'system/modules/wem-contao-smartgear/templates/backend'
]);