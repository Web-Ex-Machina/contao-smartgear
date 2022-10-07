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

namespace WEM\SmartgearBundle\Classes\DataManager;

class DataSetFinder
{
    /** @var string */
    protected $projectDir;
    protected $fullDir;

    public function __construct(
        string $projectDir
    ) {
        $this->projectDir = $projectDir;
        $this->fullDir = TL_ROOT.\DIRECTORY_SEPARATOR.$this->projectDir;
    }

    public function buildList(): array
    {
        return $this->getDataSetsFromFolder($this->fullDir);
    }

    public function getDatasetFQDNFromPath(string $filePath): string
    {
        return '\WEM\SmartgearBundle\Dataset'.str_replace([\DIRECTORY_SEPARATOR, '.php'], ['\\', ''], str_replace($this->fullDir, '', $filePath));
    }

    protected function getDataSetsFromFolder(string $folderPath): array
    {
        $templates = [];
        foreach ((new \Contao\CoreBundle\Config\ResourceFinder([$folderPath]))->find()->files()->depth('<=3')->name('DataSet.php') as $filePath => $fileInfo) {
            // $templates[$filePath] = '\WEM\SmartgearBundle\Dataset'.str_replace([\DIRECTORY_SEPARATOR, '.php'], ['\\', ''], str_replace($folderPath, '', $filePath));
            $templates[$filePath] = $this->getDatasetFQDNFromPath($filePath);
        }

        return $templates;
    }
}
