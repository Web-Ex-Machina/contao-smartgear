<?php

/**
 * SMARTGEAR for Contao Open Source CMS
 *
 * Copyright (c) 2015-2018 Web ex Machina
 *
 * @author Web ex Machina <https://www.webexmachina.fr>
 */

/**
 * Extends settings fields
 */
$GLOBALS['TL_DCA']['tl_settings']['palettes']['default'] .= ';{smartgear_legend},sgInstallComplete,sgInstallTheme,sgInstallLayout,sgInstallModules,sgInstallUserGroup,sgInstallUser,sgInstallNcGateway';

$GLOBALS['TL_DCA']['tl_settings']['fields']['sgInstallComplete'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['sgInstallComplete'],
	'inputType'               => 'checkbox',
	'eval'                    => array('tl_class'=>'clr')
);
$GLOBALS['TL_DCA']['tl_settings']['fields']['sgInstallTheme'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['sgInstallTheme'],
	'inputType'               => 'select',
	'foreignKey'			  => 'tl_theme.name',
	'eval'                    => array('includeBlankOption'=>true, 'tl_class'=>'w50')
);
$GLOBALS['TL_DCA']['tl_settings']['fields']['sgInstallLayout'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['sgInstallLayout'],
	'inputType'               => 'select',
	'foreignKey'			  => 'tl_layout.name',
	'eval'                    => array('includeBlankOption'=>true, 'tl_class'=>'w50')
);
$GLOBALS['TL_DCA']['tl_settings']['fields']['sgInstallModules'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['sgInstallModules'],
	'inputType'               => 'select',
	'foreignKey'			  => 'tl_module.name',
	'eval'                    => array('includeBlankOption'=>true, 'tl_class'=>'clr', 'multiple'=>true, 'chosen'=>true)
);
$GLOBALS['TL_DCA']['tl_settings']['fields']['sgInstallUserGroup'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['sgInstallUserGroup'],
	'inputType'               => 'select',
	'foreignKey'			  => 'tl_user_group.name',
	'eval'                    => array('includeBlankOption'=>true, 'tl_class'=>'w50')
);
$GLOBALS['TL_DCA']['tl_settings']['fields']['sgInstallUser'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['sgInstallUser'],
	'inputType'               => 'select',
	'foreignKey'			  => 'tl_user.username',
	'eval'                    => array('includeBlankOption'=>true, 'tl_class'=>'w50')
);
$GLOBALS['TL_DCA']['tl_settings']['fields']['sgInstallRootPage'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['sgInstallRootPage'],
	'inputType'               => 'select',
	'foreignKey'			  => 'tl_page.title',
	'eval'                    => array('includeBlankOption'=>true, 'tl_class'=>'w50')
);
$GLOBALS['TL_DCA']['tl_settings']['fields']['sgInstallNcGateway'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['sgInstallNcGateway'],
	'inputType'               => 'select',
	'foreignKey'			  => 'tl_nc_gateway.title',
	'eval'                    => array('includeBlankOption'=>true, 'tl_class'=>'w50')
);