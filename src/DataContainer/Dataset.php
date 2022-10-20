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

use Contao\Backend;
use Contao\CoreBundle\Exception\AccessDeniedException;
use Contao\DataContainer;
use Contao\Environment;
use Contao\Image;
use Contao\Input;
use Contao\StringUtil;
use Contao\System;
use Exception;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as CoreConfigurationManager;
use WEM\SmartgearBundle\Classes\StringUtil as ClassesStringUtil;
use WEM\SmartgearBundle\Classes\Util;
use WEM\SmartgearBundle\Model\Dataset as DatasetModel;
use WEM\SmartgearBundle\Model\DatasetInstall as DatasetInstallModel;
use WEM\SmartgearBundle\Service\DataManager as DataManagerService;

class Dataset extends Backend
{
    /** @var CoreConfigurationManager */
    private $configManager;

    public function __construct()
    {
        $this->configManager = System::getContainer()->get('smartgear.config.manager.core');
    }

    public function synchronize(DataContainer $dc): void
    {
        $dataManagerService = System::getContainer()->get('smartgear.service.data_manager');
        $dataManagerService->synchroniseDatasetListFromDiskToDb();
        $referer = preg_replace('/&(amp;)?(key)=[^&]*/', '', Environment::get('request'));

        self::redirect($referer);
    }

    public function install(DataContainer $dc)
    {
        if (!$dc->id) {
            throw new Exception('No dataset ID given');
        }

        $objDataset = DatasetModel::findByPk($dc->id);
        if (!$objDataset) {
            throw new Exception('Dataset not found');
        }

        $path = Util::getDatasetPhpFileFromRelativePath($objDataset->relative_path, true);
        /** @var DataManagerService */
        $dataManagerService = System::getContainer()->get('smartgear.service.data_manager');

        // if we need a specific configuration, open a modal with the fiels
        $dtFile = $dataManagerService->getDatasetClass($path);
        if (!empty($dtFile->getConfiguration())) {
            // open a modal with everything configured
            return $this->generateConfigurationForm($dc);
        }

        $dataManagerService->installDataset($path, []);
        $referer = preg_replace('/&(amp;)?(key)=[^&]*/', '', Environment::get('request'));

        // self::redirect($referer.'&table=tl_sm_dataset_install');
        self::redirect($referer);
    }

    public function installButton(array $row, ?string $href = '', ?string $label = '', ?string $title = '', ?string $icon = '', ?string $attributes = ''): string
    {
        $path = Util::getDatasetPhpFileFromRelativePath($row['relative_path'], true);
        /** @var DataManagerService */
        $dataManagerService = System::getContainer()->get('smartgear.service.data_manager');
        if (!$dataManagerService->canBeImported($path)) {
            return '';
        }
        $isPopup = $dataManagerService->needsConfiguration($path);
        $popupCode = $isPopup ? 'onclick="Backend.openModalIframe({\'title\':\'Installer l\\\'élément\',\'url\':this.href});return false"' : '';

        return '<a href="'.$this->addToUrl($href.'&id='.$row['id'].($isPopup ? '&amp;popup=1' : '')).'" title="'.ClassesStringUtil::specialchars($title).'" '.$attributes.' '.$popupCode.'>'.Image::getHtml($icon, $label).'</a>';
    }

    /**
     * Check permissions to edit table tl_sm_social_network.
     *
     * @throws AccessDeniedException
     */
    public function checkPermission(): void
    {
        // Check current action
        switch (Input::get('act')) {
            case 'delete':
                if ($this->isItemUsed((int) Input::get('id'))) {
                    throw new AccessDeniedException('Not enough permissions to '.Input::get('act').' dataset ID '.Input::get('id').'.');
                }
            break;
        }
    }

    /**
     * Return the delete social network button.
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
        if ($this->isItemUsed((int) $row['id'])) {
            return Image::getHtml(preg_replace('/\.svg$/i', '_.svg', $icon)).' ';
        }

        return '<a href="'.$this->addToUrl($href.'&amp;id='.$row['id']).'" title="'.StringUtil::specialchars($title).'"'.$attributes.'>'.Image::getHtml($icon, $label).'</a> ';
    }

    protected function generateConfigurationForm(DataContainer $dc)
    {
        if (!$dc->id) {
            throw new Exception('No dataset ID given');
        }

        $objDataset = DatasetModel::findByPk($dc->id);
        if (!$objDataset) {
            throw new Exception('Dataset not found');
        }

        $path = Util::getDatasetPhpFileFromRelativePath($objDataset->relative_path, true);
        /** @var DataManagerService */
        $dataManagerService = System::getContainer()->get('smartgear.service.data_manager');

        $referer = preg_replace('/&(amp;)?(key)=[^&]*/', '', Environment::get('request'));
        $str = '<form method="POST" action="'.$referer.'">';

        // if we need a specific configuration, open a modal with the fiels
        $dtFile = $dataManagerService->getDatasetClass($path);
        foreach ($dtFile->getConfiguration() as $legendLabel => $fields) {
            foreach ($fields as $fieldLabel => $fieldSettings) {
                // do something
            }
        }

        $str .= '<div class="tl_formbody_submit">
<div class="tl_submit_container">
  <input type="submit" value="'.$GLOBALS['TL_LANG']['MSC']['validate'].'">
</div>
</div>';
        $str .= '</form>';

        return $str;
    }

    /**
     * Check if the social network is being used by Smartgear.
     *
     * @param int $id social network's ID
     */
    protected function isItemUsed(int $id): bool
    {
        return DatasetInstallModel::countBy('pid', $id) > 0;
    }
}
