<?php

declare(strict_types=1);

/**
 * SMARTGEAR for Contao Open Source CMS
 * Copyright (c) 2015-2022 Web ex Machina
 *
 * @category ContaoBundle
 * @package  Web-Ex-Machina/contao-smartgear
 * @author   Web ex Machina <contact@webexmachina.fr>
 * @link     https://github.com/Web-Ex-Machina/contao-smartgear/
 */

/*
 * Register the templates.
 */
Contao\TemplateLoader::addFiles(
    [
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
