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
$GLOBALS['TL_DCA']['tl_settings']['palettes']['default'] .= ';{smartgear_legend},sgInstallComplete,sgInstallTheme,sgInstallLayout,sgInstallModules,sgInstallUserGroup,sgInstallUser,sgInstallRootPage,sgInstallNcGateway';
$GLOBALS['TL_DCA']['tl_settings']['palettes']['default'] .= ';{sgblog_legend},sgBlogInstall,sgBlogNewsArchive,sgBlogModuleList,sgBlogModuleReader,sgBlogPageList,sgBlogPageReader';
$GLOBALS['TL_DCA']['tl_settings']['palettes']['default'] .= ';{sgcalendar_legend},sgCalendarInstall,sgCalendar,sgCalendarModuleList,sgCalendarModuleListPassed,sgCalendarModuleReader,sgCalendarPageList,sgCalendarPageReader';
<<<<<<< HEAD
$GLOBALS['TL_DCA']['tl_settings']['palettes']['default'] .= ';{sgfaq_legend},sgFAQInstall,sgFAQ,sgFAQModule,sgFAQPage';
=======
>>>>>>> bdccee3ec2e58d1bb209f84accd2d74c1b8d0cbd

/**
 * ;{smartgear_legend},sgInstallComplete,sgInstallTheme,sgInstallLayout,sgInstallModules,sgInstallUserGroup,sgInstallUser,sgInstallNcGateway
 */
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

/**
 * ;{sgblog_legend},sgBlogInstall,sgBlogNewsArchive,sgBlogModuleList,sgBlogModuleReader,sgBlogPageList,sgBlogPageReader
 */
$GLOBALS['TL_DCA']['tl_settings']['fields']['sgBlogInstall'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['sgBlogInstall'],
	'inputType'               => 'checkbox',
	'eval'                    => array('tl_class'=>'clr')
);
$GLOBALS['TL_DCA']['tl_settings']['fields']['sgBlogNewsArchive'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['sgBlogNewsArchive'],
	'inputType'               => 'select',
	'foreignKey'			  => 'tl_news_archive.title',
	'eval'                    => array('includeBlankOption'=>true, 'tl_class'=>'')
);
$GLOBALS['TL_DCA']['tl_settings']['fields']['sgBlogModuleList'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['sgBlogModuleList'],
	'inputType'               => 'select',
	'foreignKey'			  => 'tl_module.name',
	'eval'                    => array('includeBlankOption'=>true, 'tl_class'=>'w50')
);
$GLOBALS['TL_DCA']['tl_settings']['fields']['sgBlogModuleReader'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['sgBlogModuleReader'],
	'inputType'               => 'select',
	'foreignKey'			  => 'tl_module.name',
	'eval'                    => array('includeBlankOption'=>true, 'tl_class'=>'w50')
);
$GLOBALS['TL_DCA']['tl_settings']['fields']['sgBlogPageList'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['sgBlogPageList'],
	'inputType'               => 'select',
	'foreignKey'			  => 'tl_page.title',
	'eval'                    => array('includeBlankOption'=>true, 'tl_class'=>'w50')
);
$GLOBALS['TL_DCA']['tl_settings']['fields']['sgBlogPageReader'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['sgBlogPageReader'],
	'inputType'               => 'select',
	'foreignKey'			  => 'tl_page.title',
	'eval'                    => array('includeBlankOption'=>true, 'tl_class'=>'w50')
);

// {sgcalendar_legend},sgCalendarInstall,sgCalendar,sgCalendarModuleList,sgCalendarModuleListPassed,sgCalendarModuleReader,sgCalendarPageList,sgCalendarPageReader
$GLOBALS['TL_DCA']['tl_settings']['fields']['sgCalendarInstall'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['sgCalendarInstall'],
	'inputType'               => 'checkbox',
	'eval'                    => array('tl_class'=>'clr')
);
$GLOBALS['TL_DCA']['tl_settings']['fields']['sgCalendar'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['sgCalendar'],
	'inputType'               => 'select',
	'foreignKey'			  => 'tl_calendar.title',
	'eval'                    => array('includeBlankOption'=>true, 'tl_class'=>'')
);
$GLOBALS['TL_DCA']['tl_settings']['fields']['sgCalendarModuleList'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['sgCalendarModuleList'],
	'inputType'               => 'select',
	'foreignKey'			  => 'tl_module.name',
	'eval'                    => array('includeBlankOption'=>true, 'tl_class'=>'w50')
);
$GLOBALS['TL_DCA']['tl_settings']['fields']['sgCalendarModuleListPassed'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['sgCalendarModuleListPassed'],
	'inputType'               => 'select',
	'foreignKey'			  => 'tl_module.name',
	'eval'                    => array('includeBlankOption'=>true, 'tl_class'=>'w50')
);
$GLOBALS['TL_DCA']['tl_settings']['fields']['sgCalendarModuleReader'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['sgCalendarModuleReader'],
	'inputType'               => 'select',
	'foreignKey'			  => 'tl_module.name',
	'eval'                    => array('includeBlankOption'=>true, 'tl_class'=>'w50')
);
$GLOBALS['TL_DCA']['tl_settings']['fields']['sgCalendarPageList'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['sgCalendarPageList'],
	'inputType'               => 'select',
	'foreignKey'			  => 'tl_page.title',
	'eval'                    => array('includeBlankOption'=>true, 'tl_class'=>'w50')
);
$GLOBALS['TL_DCA']['tl_settings']['fields']['sgCalendarPageReader'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['sgCalendarPageReader'],
	'inputType'               => 'select',
	'foreignKey'			  => 'tl_page.title',
	'eval'                    => array('includeBlankOption'=>true, 'tl_class'=>'w50')
<<<<<<< HEAD
);

// $GLOBALS['TL_DCA']['tl_settings']['palettes']['default'] .= ';{sgfaq_legend},sgFAQInstall,sgFAQ,sgFAQModule,sgFAQPage';
$GLOBALS['TL_DCA']['tl_settings']['fields']['sgFAQInstall'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['sgFAQInstall'],
	'inputType'               => 'checkbox',
	'eval'                    => array('tl_class'=>'clr')
);
$GLOBALS['TL_DCA']['tl_settings']['fields']['sgFAQ'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['sgFAQ'],
	'inputType'               => 'select',
	'foreignKey'			  => 'tl_faq_category.title',
	'eval'                    => array('includeBlankOption'=>true, 'tl_class'=>'')
);
$GLOBALS['TL_DCA']['tl_settings']['fields']['sgFAQModule'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['sgFAQModule'],
	'inputType'               => 'select',
	'foreignKey'			  => 'tl_module.name',
	'eval'                    => array('includeBlankOption'=>true, 'tl_class'=>'w50')
);
$GLOBALS['TL_DCA']['tl_settings']['fields']['sgFAQPage'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['sgFAQPage'],
	'inputType'               => 'select',
	'foreignKey'			  => 'tl_page.title',
	'eval'                    => array('includeBlankOption'=>true, 'tl_class'=>'w50')
=======
>>>>>>> bdccee3ec2e58d1bb209f84accd2d74c1b8d0cbd
);