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

namespace WEM\SmartgearBundle\Classes;

use Contao\ThemeModel;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as CoreConfigurationManager;
use WEM\SmartgearBundle\Config\Component\Core\Core as CoreConfig;
use WEM\SmartgearBundle\Exceptions\File\NotFound;

class TemplateFinder
{
    /** @var string */
    protected $projectDir;
    /** @var CoreConfigurationManager */
    protected $configurationManager;

    public function __construct(
        string $projectDir,
        CoreConfigurationManager $configurationManager
    ) {
        $this->projectDir = $projectDir;
        $this->configurationManager = $configurationManager;
    }

    public function buildList(?string $clientTemplatesFolderName = ''): array
    {
        // array_merge(arr1, arr2) will overwrite arr1 keys by arr2 keys if equals
        // so we start by the least important

        return array_merge($this->getSmartgearTemplates(), $this->getRsceTemplates(), $this->getClientTemplates($clientTemplatesFolderName), $this->getRootTemplates());
    }

    protected function getRsceTemplates(): array
    {
        return file_exists($this->projectDir.\DIRECTORY_SEPARATOR.'templates'.\DIRECTORY_SEPARATOR.'rsce') ? $this->getTemplatesFromFolder($this->projectDir.\DIRECTORY_SEPARATOR.'templates'.\DIRECTORY_SEPARATOR.'rsce') : [];
    }

    protected function getRootTemplates(): array
    {
        return file_exists($this->projectDir.\DIRECTORY_SEPARATOR.'templates') ? $this->getTemplatesFromFolder($this->projectDir.\DIRECTORY_SEPARATOR.'templates') : [];
    }

    protected function getSmartgearTemplates(): array
    {
        return file_exists($this->projectDir.\DIRECTORY_SEPARATOR.'templates'.\DIRECTORY_SEPARATOR.'smartgear') ? $this->getTemplatesFromFolder($this->projectDir.\DIRECTORY_SEPARATOR.'templates'.\DIRECTORY_SEPARATOR.'smartgear') : [];
    }

    protected function getClientTemplates(?string $clientTemplatesFolderName = ''): array
    {
        if ('' === $clientTemplatesFolderName) {
            // get the core config, get the theme_id, retrieve the theme and use the path in "templates" field
            try {
                /** @var CoreConfig $config */
                $config = $this->configurationManager->load();
            } catch (NotFound) {
                return [];
            }

            $objTheme = ThemeModel::findById($config->getSgTheme());

            return file_exists($this->projectDir.\DIRECTORY_SEPARATOR.$objTheme->templates) ? $this->getTemplatesFromFolder($this->projectDir.\DIRECTORY_SEPARATOR.$objTheme->templates) : [];
        }

        return file_exists($this->projectDir.\DIRECTORY_SEPARATOR.$clientTemplatesFolderName) ? $this->getTemplatesFromFolder($this->projectDir.\DIRECTORY_SEPARATOR.$clientTemplatesFolderName) : [];
    }

    protected function getTemplatesFromFolder(string $folderPath): array
    {
        $templates = [];
        foreach ((new \Contao\CoreBundle\Config\ResourceFinder([$folderPath]))->find()->files()->depth('==0')->name('*.html5') as $filePath => $fileInfo) {
            $templates[str_replace('.html5', '', $fileInfo->getFilename())] = str_replace($this->projectDir.\DIRECTORY_SEPARATOR, '', $folderPath);
        }

        return $templates;
    }
}
