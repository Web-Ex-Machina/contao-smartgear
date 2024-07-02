<?php

declare(strict_types=1);

/**
 * SMARTGEAR for Contao Open Source CMS
 * Copyright (c) 2015-2023 Web ex Machina
 *
 * @category ContaoBundle
 * @package  Web-Ex-Machina/contao-smartgear
 * @author   Web ex Machina <contact@webexmachina.fr>
 * @link     https://github.com/Web-Ex-Machina/contao-smartgear/
 */

namespace WEM\SmartgearBundle\DataContainer;

use Contao\CoreBundle\Csrf\ContaoCsrfTokenManager;
use Contao\CoreBundle\Exception\AccessDeniedException;
use Contao\CoreBundle\Security\ContaoCorePermissions;
use Contao\DataContainer;
use Contao\Image;
use Contao\Input;
use Contao\StringUtil;
use Contao\System;
use WEM\SmartgearBundle\Model\Configuration\ConfigurationItem;

class Module extends \tl_module
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Return the edit module wizard.
     *
     * @return string
     */
    public function editModule(DataContainer $dc)
    {
        /* @var ContaoCsrfTokenManager $contaoCsrfTokenManager */
        $contaoCsrfTokenManager = System::getContainer()->getParameter('contao.csrf.token_manager');
        $contaoCsrfTokenManager->getDefaultTokenValue();
        return ($dc->value < 1) ? '' : ' <a href="contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id='.$dc->value.'&amp;popup=1&amp;nb=1&amp;rt='.$contaoCsrfTokenManager->getDefaultTokenValue().'" title="'.sprintf(StringUtil::specialchars($GLOBALS['TL_LANG']['tl_content']['editalias'][1]), $dc->value).'" onclick="Backend.openModalIframe({\'title\':\''.StringUtil::specialchars(str_replace("'", "\\'", sprintf($GLOBALS['TL_LANG']['tl_content']['editalias'][1], $dc->value))).'\',\'url\':this.href});return false">'.Image::getHtml('alias.svg', $GLOBALS['TL_LANG']['tl_content']['editalias'][0]).'</a>';
    }

    /**
     * Get all modules and return them as array.
     *
     * @return array
     */
    public function getModules()
    {
        $arrModules = [];
        $objModules = $this->Database->execute(sprintf('SELECT m.id, m.name, t.name AS theme FROM tl_module m LEFT JOIN tl_theme t ON m.pid=t.id WHERE m.id != %s ORDER BY t.name, m.name', Input::get('id')));

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
        parent::checkPermission(); //todo : not found ??

        if (Input::get('act') === 'delete' && !$this->canItemBeDeleted((int) Input::get('id'))) {
            throw new AccessDeniedException('Not enough permissions to '.Input::get('act').' module ID '.Input::get('id').'.');
        }
    }

    /**
     * Return the delete module button.
     */
    public function deleteItem(array $row, string $href, string $label, string $title, string $icon, string $attributes): string
    {
        if (!$this->canItemBeDeleted((int) $row['id'])) {
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
        // try {
        //     /** @var CoreConfig $config */
        //     $config = $this->configManager->load();
        //     if (\in_array($id, $config->getContaoModulesIdsForAll(), true)) {
        //         return true;
        //     }
        // } catch (\Exception $e) {
        // }
        return 0 < ConfigurationItem::countItems(['contao_module' => $id])
        || 0 < ConfigurationItem::countItems(['contao_module_reader' => $id])
        || 0 < ConfigurationItem::countItems(['contao_module_list' => $id])
        || 0 < ConfigurationItem::countItems(['contao_module_calendar' => $id]);
    }

    protected function canItemBeDeleted(int $id): bool
    {
        return $this->User->admin || !$this->isItemUsedBySmartgear($id);
    }
}
