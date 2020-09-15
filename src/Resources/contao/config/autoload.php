<?php

declare(strict_types=1);

/**
 * SMARTGEAR for Contao Open Source CMS
 * Copyright (c) 2015-2020 Web ex Machina
 *
 * @category ContaoBundle
 * @package  Web-Ex-Machina/contao-smartgear
 * @author   Web ex Machina <contact@webexmachina.fr>
 * @link     https://github.com/Web-Ex-Machina/contao-smartgear/
 */

/*
 * Register the templates.
 */
TemplateLoader::addFiles(
    [
        // RSCE Templates
        'rsce_accordionFW' => 'templates/rsce',
        'rsce_block-img' => 'templates/rsce',
        'rsce_counterFW' => 'templates/rsce',
        'rsce_foldingbox' => 'templates/rsce',
        'rsce_gridGallery' => 'templates/rsce',
        'rsce_heroFW' => 'templates/rsce',
        'rsce_heroFWStart' => 'templates/rsce',
        'rsce_heroFWStop' => 'templates/rsce',
        'rsce_listIcons' => 'templates/rsce',
        'rsce_notations' => 'templates/rsce',
        'rsce_priceCards' => 'templates/rsce',
        'rsce_quote' => 'templates/rsce',
        'rsce_sliderFW' => 'templates/rsce',
        'rsce_tabs' => 'templates/rsce',
        'rsce_testimonials' => 'templates/rsce',
        'rsce_timeline' => 'templates/rsce',

        // Backend Templates
        'be_wem_sg_install' => 'system/modules/wem-contao-smartgear/templates/backend',
        'be_wem_sg_install_block_default' => 'system/modules/wem-contao-smartgear/templates/backend',
        'be_wem_sg_install_block_core_core' => 'system/modules/wem-contao-smartgear/templates/backend',
        'be_wem_sg_module' => 'system/modules/wem-contao-smartgear/templates/backend',

        // Components Templates
        'mod_wem_sg_header' => 'system/modules/wem-contao-smartgear/templates/components',

        // Backend Modals
        'be_wem_sg_install_modal_core_configure' => 'system/modules/wem-contao-smartgear/templates/backend/modals',
        'be_wem_sg_install_modal_blog_configure' => 'system/modules/wem-contao-smartgear/templates/backend/modals',
    ]
);
