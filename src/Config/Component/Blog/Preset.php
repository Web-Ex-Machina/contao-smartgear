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
    public const ARCHIVE_MODE_EMPTY = '';
    public const ARCHIVE_MODE_ARCHIVE = 'archive';
    public const ARCHIVE_MODE_KEEP = 'keep';
    public const ARCHIVE_MODE_DELETE = 'delete';
    public const ARCHIVE_MODES_ALLOWED = [
        self::ARCHIVE_MODE_EMPTY,
        self::ARCHIVE_MODE_ARCHIVE,
        self::ARCHIVE_MODE_KEEP,
        self::ARCHIVE_MODE_DELETE,
    ];
    public const DEFAULT_FOLDER_PATH = 'files/news';
    public const DEFAULT_NEWS_PER_PAGE = 15;
    public const DEFAULT_PAGE_TITLE = 'Blog';
    public const DEFAULT_ARCHIVE_MODE = self::ARCHIVE_MODE_EMPTY;
    /** @var string */
    protected $sgNewsFolder = self::DEFAULT_FOLDER_PATH;
    /** @var string */
    protected $sgNewsArchiveTitle = '';
    /** @var int */
    protected $sgNewsListPerPage = self::DEFAULT_NEWS_PER_PAGE;
    /** @var string */
    protected $sgPageTitle = self::DEFAULT_PAGE_TITLE;
    /** @var bool */
    protected $sgArchived = false;
    /** @var int */
    protected $sgArchivedAt = 0;
    /** @var string */
    protected $sgArchivedMode = self::DEFAULT_ARCHIVE_MODE;

    public function reset(): self
    {
        $this
            ->setSgNewsFolder(self::DEFAULT_FOLDER_PATH)
            ->setSgNewsArchiveTitle('')
            ->setSgNewsListPerPage(self::DEFAULT_NEWS_PER_PAGE)
            ->setSgPageTitle(self::DEFAULT_PAGE_TITLE)
            ->setSgArchived(false)
            ->setSgArchivedAt(0)
            ->setSgArchivedMode(self::DEFAULT_ARCHIVE_MODE)
        ;

        return $this;
    }

    public function import(\stdClass $json): self
    {
        $this
            ->setSgNewsFolder($json->folder ?? self::DEFAULT_FOLDER_PATH)
            ->setSgNewsArchiveTitle($json->archive_title ?? '')
            ->setSgNewsListPerPage($json->list_per_page ?? self::DEFAULT_NEWS_PER_PAGE)
            ->setSgPageTitle($json->page_title ?? self::DEFAULT_PAGE_TITLE)
            ->setSgArchived($json->archived->status ?? false)
            ->setSgArchivedAt($json->archived->at ?? 0)
            ->setSgArchivedMode($json->archived->mode ?? self::DEFAULT_ARCHIVE_MODE)
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

        $json->archived = new \stdClass();
        $json->archived->status = $this->getSgArchived();
        $json->archived->at = $this->getSgArchivedAt();
        $json->archived->mode = $this->getSgArchivedMode();

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

    public function getSgArchived(): bool
    {
        return $this->sgArchived;
    }

    public function setSgArchived(bool $sgArchived): self
    {
        $this->sgArchived = $sgArchived;

        return $this;
    }

    public function getSgArchivedAt(): int
    {
        return $this->sgArchivedAt;
    }

    public function setSgArchivedAt(int $sgArchivedAt): self
    {
        $this->sgArchivedAt = $sgArchivedAt;

        return $this;
    }

    public function getSgArchivedMode(): string
    {
        return $this->sgArchivedMode;
    }

    public function setSgArchivedMode(string $sgArchivedMode): self
    {
        if (!\in_array($sgArchivedMode, static::ARCHIVE_MODES_ALLOWED, true)) {
            throw new \InvalidArgumentException(sprintf('Invalid archive mode "%s" given', $sgArchivedMode));
        }
        $this->sgArchivedMode = $sgArchivedMode;

        return $this;
    }
}
