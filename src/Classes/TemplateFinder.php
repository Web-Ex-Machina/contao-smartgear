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

namespace WEM\SmartgearBundle\Classes;

// use Contao\CoreBundle\Config\ResourceFinder;
use Contao\StringUtil;
use Contao\ThemeModel;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as CoreConfigurationManager;
use WEM\SmartgearBundle\Config\Core as CoreConfig;
use WEM\SmartgearBundle\Exceptions\File\NotFound;
use WEM\UtilsBundle\Classes\StringUtil as WEMStringUtil;

class TemplateFinder
{
    /** @var string */
    protected $projectDir;
    /** @var ResourceFinder */
    // protected $resourceFinder;
    /** @var CoreConfigurationManager */
    protected $configurationManager;

    public function __construct(
        string $projectDir,
        // ResourceFinder $resourceFinder,
        CoreConfigurationManager $configurationManager
    ) {
        $this->projectDir = $projectDir;
        // $this->resourceFinder = $resourceFinder;
        $this->configurationManager = $configurationManager;
    }

    public function coucou(): string
    {
        return 'coucou';
    }

    public function buildList(): array
    {
        // array_merge(arr1, arr2) will overwrite arr1 keys by arr2 keys if equals
        // so we start by the least important

        return array_merge($this->getSmartgearTemplates(), $this->getRsceTemplates(), $this->getClientTemplates(), $this->getRootTemplates());
    }

    protected function getRsceTemplates(): array
    {
        return $this->getTemplatesFromFolder($this->projectDir.\DIRECTORY_SEPARATOR.'templates'.\DIRECTORY_SEPARATOR.'rsce');
    }

    protected function getRootTemplates(): array
    {
        return $this->getTemplatesFromFolder($this->projectDir.\DIRECTORY_SEPARATOR.'templates');
    }

    protected function getSmartgearTemplates(): array
    {
        return $this->getTemplatesFromFolder($this->projectDir.\DIRECTORY_SEPARATOR.'templates'.\DIRECTORY_SEPARATOR.'smartgear');
    }

    protected function getClientTemplates(): array
    {
        // get the core config, get the theme_id, retrieve the theme and use the path in "templates" field
        try {
            /** @var CoreConfig */
            $config = $this->configurationManager->load();
        } catch (NotFound $e) {
            return [];
        }

        $uselessVarToMakeItWork = StringUtil::ampersand('ok');
        // $objTheme = ThemeModel::findById($config->getSgTheme());

        // return $this->getTemplatesFromFolder($this->projectDir.\DIRECTORY_SEPARATOR.$objTheme->templates);

        return $this->getTemplatesFromFolder($this->projectDir.\DIRECTORY_SEPARATOR.'templates'.\DIRECTORY_SEPARATOR.WEMStringUtil::generateAlias($config->getSgWebsiteTitle()));
    }

    protected function getTemplatesFromFolder(string $folderPath): array
    {
        $templates = [];
        if (is_dir($folderPath)) {
            $files = scandir($folderPath);
            foreach ($files as $filename) {
                if (\strlen($filename) > 6
                && '.html5' === substr($filename, -6, 6)) {
                    $templates[str_replace('.html5', '', $filename)] = str_replace($this->projectDir.\DIRECTORY_SEPARATOR, '', $folderPath);
                }
            }
        }

        return $templates;
    }
}
