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

use WEM\SmartgearBundle\Classes\Dca\Driver\DC_Table;

$GLOBALS['TL_DCA']['tl_member']['config']['dataContainer'] = DC_Table::class;
$GLOBALS['TL_DCA']['tl_member']['config']['onshow_callback'][] = ['wem.personal_data_manager.dca.config.callback.show', '__invoke'];
$GLOBALS['TL_DCA']['tl_member']['config']['ondelete_callback'][] = ['wem.personal_data_manager.dca.config.callback.delete', '__invoke'];
$GLOBALS['TL_DCA']['tl_member']['list']['label']['label_callback'] = ['wem.personal_data_manager.dca.listing.callback.list_label_label_for_list', '__invoke'];
$GLOBALS['TL_DCA']['tl_member']['list']['label']['group_callback'] = ['wem.personal_data_manager.dca.listing.callback.list_label_group', '__invoke'];

$GLOBALS['TL_DCA']['tl_member']['fields']['firstname']['load_callback'][] = ['wem.personal_data_manager.dca.field.callback.load', '__invoke'];
$GLOBALS['TL_DCA']['tl_member']['fields']['firstname']['save_callback'][] = ['wem.personal_data_manager.dca.field.callback.save', '__invoke'];
$GLOBALS['TL_DCA']['tl_member']['fields']['lastname']['load_callback'][] = ['wem.personal_data_manager.dca.field.callback.load', '__invoke'];
$GLOBALS['TL_DCA']['tl_member']['fields']['lastname']['save_callback'][] = ['wem.personal_data_manager.dca.field.callback.save', '__invoke'];
$GLOBALS['TL_DCA']['tl_member']['fields']['dateOfBirth']['load_callback'][] = ['wem.personal_data_manager.dca.field.callback.load', '__invoke'];
$GLOBALS['TL_DCA']['tl_member']['fields']['dateOfBirth']['save_callback'][] = ['wem.personal_data_manager.dca.field.callback.save', '__invoke'];
$GLOBALS['TL_DCA']['tl_member']['fields']['gender']['load_callback'][] = ['wem.personal_data_manager.dca.field.callback.load', '__invoke'];
$GLOBALS['TL_DCA']['tl_member']['fields']['gender']['save_callback'][] = ['wem.personal_data_manager.dca.field.callback.save', '__invoke'];
$GLOBALS['TL_DCA']['tl_member']['fields']['company']['load_callback'][] = ['wem.personal_data_manager.dca.field.callback.load', '__invoke'];
$GLOBALS['TL_DCA']['tl_member']['fields']['company']['save_callback'][] = ['wem.personal_data_manager.dca.field.callback.save', '__invoke'];
$GLOBALS['TL_DCA']['tl_member']['fields']['street']['load_callback'][] = ['wem.personal_data_manager.dca.field.callback.load', '__invoke'];
$GLOBALS['TL_DCA']['tl_member']['fields']['street']['save_callback'][] = ['wem.personal_data_manager.dca.field.callback.save', '__invoke'];
$GLOBALS['TL_DCA']['tl_member']['fields']['postal']['load_callback'][] = ['wem.personal_data_manager.dca.field.callback.load', '__invoke'];
$GLOBALS['TL_DCA']['tl_member']['fields']['postal']['save_callback'][] = ['wem.personal_data_manager.dca.field.callback.save', '__invoke'];
$GLOBALS['TL_DCA']['tl_member']['fields']['city']['load_callback'][] = ['wem.personal_data_manager.dca.field.callback.load', '__invoke'];
$GLOBALS['TL_DCA']['tl_member']['fields']['city']['save_callback'][] = ['wem.personal_data_manager.dca.field.callback.save', '__invoke'];
$GLOBALS['TL_DCA']['tl_member']['fields']['state']['load_callback'][] = ['wem.personal_data_manager.dca.field.callback.load', '__invoke'];
$GLOBALS['TL_DCA']['tl_member']['fields']['state']['save_callback'][] = ['wem.personal_data_manager.dca.field.callback.save', '__invoke'];
$GLOBALS['TL_DCA']['tl_member']['fields']['country']['load_callback'][] = ['wem.personal_data_manager.dca.field.callback.load', '__invoke'];
$GLOBALS['TL_DCA']['tl_member']['fields']['country']['save_callback'][] = ['wem.personal_data_manager.dca.field.callback.save', '__invoke'];
$GLOBALS['TL_DCA']['tl_member']['fields']['phone']['load_callback'][] = ['wem.personal_data_manager.dca.field.callback.load', '__invoke'];
$GLOBALS['TL_DCA']['tl_member']['fields']['phone']['save_callback'][] = ['wem.personal_data_manager.dca.field.callback.save', '__invoke'];
$GLOBALS['TL_DCA']['tl_member']['fields']['mobile']['load_callback'][] = ['wem.personal_data_manager.dca.field.callback.load', '__invoke'];
$GLOBALS['TL_DCA']['tl_member']['fields']['mobile']['save_callback'][] = ['wem.personal_data_manager.dca.field.callback.save', '__invoke'];
$GLOBALS['TL_DCA']['tl_member']['fields']['fax']['load_callback'][] = ['wem.personal_data_manager.dca.field.callback.load', '__invoke'];
$GLOBALS['TL_DCA']['tl_member']['fields']['fax']['save_callback'][] = ['wem.personal_data_manager.dca.field.callback.save', '__invoke'];
