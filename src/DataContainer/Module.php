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

namespace WEM\SmartgearBundle\DataContainer;

use Contao\CoreBundle\Exception\AccessDeniedException;
use Contao\CoreBundle\Security\ContaoCorePermissions;
use Contao\DataContainer;
use Contao\Image;
use Contao\Input;
use Contao\StringUtil;
use Contao\System;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as CoreConfigurationManager;
use WEM\SmartgearBundle\Config\Component\Core\Core as CoreConfig;

class Module extends \tl_module
{
    /** @var CoreConfigurationManager */
    private $configManager;

    public function __construct()
    {
        parent::__construct();
        $this->configManager = System::getContainer()->get('smartgear.config.manager.core');
    }

    /**
     * Return the edit module wizard.
     *
     * @return string
     */
    public function editModule(DataContainer $dc)
    {
        return ($dc->value < 1) ? '' : ' <a href="contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id='.$dc->value.'&amp;popup=1&amp;nb=1&amp;rt='.REQUEST_TOKEN.'" title="'.sprintf(StringUtil::specialchars($GLOBALS['TL_LANG']['tl_content']['editalias'][1]), $dc->value).'" onclick="Backend.openModalIframe({\'title\':\''.StringUtil::specialchars(str_replace("'", "\\'", sprintf($GLOBALS['TL_LANG']['tl_content']['editalias'][1], $dc->value))).'\',\'url\':this.href});return false">'.Image::getHtml('alias.svg', $GLOBALS['TL_LANG']['tl_content']['editalias'][0]).'</a>';
    }

    /**
     * Get all modules and return them as array.
     *
     * @return array
     */
    public function getModules()
    {
        $arrModules = [];
        $objModules = $this->Database->execute(sprintf('SELECT m.id, m.name, t.name AS theme FROM tl_module m LEFT JOIN tl_theme t ON m.pid=t.id WHERE m.id != %s ORDER BY t.name, m.name', \Input::get('id')));

        while ($objModules->next()) {
            $arrModules[$objModules->theme][$objModules->id] = $objModules->name.' (ID '.$objModules->id.')';
        }

        return $arrModules;
    }

    /**
     * Check permissions to edit table tl_module.
     *
     * @throws AccessDeniedException
     */
    public function checkPermission(): void
    {
        parent::checkPermission();

        // Check current action
        switch (Input::get('act')) {
            case 'delete':
                if ($this->isItemUsedBySmartgear((int) Input::get('id'))) {
                    throw new AccessDeniedException('Not enough permissions to '.Input::get('act').' module ID '.Input::get('id').'.');
                }
            break;
        }
    }

    /**
     * Return the delete module button.
     *
     * @param array  $row
     * @param string $href
     * @param string $label
     * @param string $title
     * @param string $icon
     * @param string $attributes
     *
     * @return string
     */
    public function deleteItem($row, $href, $label, $title, $icon, $attributes)
    {
        if ($this->isItemUsedBySmartgear((int) $row['id'])) {
            return Image::getHtml(preg_replace('/\.svg$/i', '_.svg', $icon)).' ';
        }

        return System::getContainer()->get('security.helper')->isGranted(ContaoCorePermissions::USER_CAN_ACCESS_FRONTEND_MODULES) ? '<a href="'.$this->addToUrl($href.'&amp;id='.$row['id']).'" title="'.StringUtil::specialchars($title).'"'.$attributes.'>'.Image::getHtml($icon, $label).'</a> ' : Image::getHtml(preg_replace('/\.svg$/i', '_.svg', $icon)).' ';
    }

    /**
     * Return the list of content elements for breadcrumb placement.
     */
    public function getOptionsForBreadcrumbAutoPlacementAfterContentElements(DataContainer $dc): array
    {
        $arrOptions = [];

        foreach ($GLOBALS['TL_CTE'] as $k => $v) {
            foreach (array_keys($v) as $kk) {
                $arrOptions[$GLOBALS['TL_LANG']['CTE'][$k]][$kk] = $GLOBALS['TL_LANG']['CTE'][$kk][0].' ('.$kk.')';
            }
        }

        return $arrOptions;
    }

    /**
     * Return the list of modules for breadcrumb placement.
     */
    public function getOptionsForBreadcrumbAutoPlacementAfterModules(DataContainer $dc): array
    {
        $arrOptions = [];

        foreach ($GLOBALS['FE_MOD'] as $k => $v) {
            foreach (array_keys($v) as $kk) {
                $arrOptions[$GLOBALS['TL_LANG']['FMD'][$k]][$kk] = $GLOBALS['TL_LANG']['FMD'][$kk][0].' ('.$kk.')';
            }
        }

        return $arrOptions;
    }

    /**
     * Check if the module is being used by Smartgear.
     *
     * @param int $id module's ID
     */
    protected function isItemUsedBySmartgear(int $id): bool
    {
        try {
            /** @var CoreConfig */
            $config = $this->configManager->load();
            if (\in_array($id, $config->getContaoModulesIdsForAll(), true)) {
                return true;
            }
        } catch (\Exception $e) {
        }

        return false;
    }
}
