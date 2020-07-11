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

$GLOBALS['TL_DCA']['tl_content']['fields']['customTpl']['options_callback'] = static function (Contao\DataContainer $dc) {
    return WEM\SmartgearBundle\Override\Controller::getTemplateGroup('ce_'.$dc->activeRecord->type.'_', [], 'ce_'.$dc->activeRecord->type);
};
$GLOBALS['TL_DCA']['tl_content']['fields']['customTpl']['eval']['includeBlankOption'] = true;
