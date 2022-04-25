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

namespace WEM\SmartgearBundle\Config\Component\Blog;

use WEM\SmartgearBundle\Classes\Config\ConfigJsonInterface;

class Preset implements ConfigJsonInterface
{
    public const DEFAULT_FOLDER_PATH = 'files/news';
    public const DEFAULT_NEWS_PER_PAGE = 15;
    public const DEFAULT_PAGE_TITLE = 'Blog';
    public const DEFAULT_ARCHIVE_TITLE = 'Blog';
    /** @var string */
    protected $sgNewsFolder = self::DEFAULT_FOLDER_PATH;
    /** @var string */
    protected $sgNewsArchiveTitle = self::DEFAULT_ARCHIVE_TITLE;
    /** @var int */
    protected $sgNewsListPerPage = self::DEFAULT_NEWS_PER_PAGE;
    /** @var string */
    protected $sgPageTitle = self::DEFAULT_PAGE_TITLE;
    public function reset(): self
    {
        $this
            ->setSgNewsFolder(self::DEFAULT_FOLDER_PATH)
            ->setSgNewsArchiveTitle(self::DEFAULT_ARCHIVE_TITLE)
            ->setSgNewsListPerPage(self::DEFAULT_NEWS_PER_PAGE)
            ->setSgPageTitle(self::DEFAULT_PAGE_TITLE)
        ;

        return $this;
    }

    public function import(\stdClass $json): self
    {
        $this
            ->setSgNewsFolder($json->folder ?? self::DEFAULT_FOLDER_PATH)
            ->setSgNewsArchiveTitle($json->archive_title ?? self::DEFAULT_ARCHIVE_TITLE)
            ->setSgNewsListPerPage($json->list_per_page ?? self::DEFAULT_NEWS_PER_PAGE)
            ->setSgPageTitle($json->page_title ?? self::DEFAULT_PAGE_TITLE)
        ;

        return $this;
    }

    public function export(): \stdClass
    {
        $json = new \stdClass();
        $json->folder = $this->getSgNewsFolder();
        $json->archive_title = $this->getSgNewsArchiveTitle();
        $json->list_per_page = $this->getSgNewsListPerPage();
        $json->page_title = $this->getSgPageTitle();

        return $json;
    }

    public function getSgNewsFolder(): string
    {
        return $this->sgNewsFolder;
    }

    public function setSgNewsFolder(string $sgNewsFolder): self
    {
        $this->sgNewsFolder = $sgNewsFolder;

        return $this;
    }

    public function getSgNewsArchiveTitle(): string
    {
        return $this->sgNewsArchiveTitle;
    }

    public function setSgNewsArchiveTitle(string $sgNewsArchiveTitle): self
    {
        $this->sgNewsArchiveTitle = $sgNewsArchiveTitle;

        return $this;
    }

    /**
     * @return int
     */
    public function getSgNewsListPerPage()
    {
        return $this->sgNewsListPerPage;
    }

    public function setSgNewsListPerPage(int $sgNewsListPerPage): self
    {
        $this->sgNewsListPerPage = $sgNewsListPerPage;

        return $this;
    }

    /**
     * @return string
     */
    public function getSgPageTitle()
    {
        return $this->sgPageTitle;
    }

    public function setSgPageTitle(string $sgPageTitle): self
    {
        $this->sgPageTitle = $sgPageTitle;

        return $this;
    }
}
