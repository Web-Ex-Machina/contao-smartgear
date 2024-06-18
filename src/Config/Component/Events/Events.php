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

namespace WEM\SmartgearBundle\Config\Component\Events;

use WEM\SmartgearBundle\Classes\Config\ConfigModuleInterface;

class Events implements ConfigModuleInterface
{
    public const MODE_SIMPLE = 'simple';

    public const MODE_EXPERT = 'expert';

    public const MODES_ALLOWED = [
        self::MODE_SIMPLE,
        self::MODE_EXPERT,
    ];

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

    public const DEFAULT_FOLDER_PATH = 'files/events';

    public const DEFAULT_EVENTS_PER_PAGE = 15;

    public const DEFAULT_PAGE_TITLE = 'Agenda';

    public const DEFAULT_FEED_TITLE = 'Agenda';

    public const DEFAULT_MODE = self::MODE_SIMPLE;

    public const DEFAULT_ARCHIVE_MODE = self::ARCHIVE_MODE_EMPTY;

    protected bool $sgInstallComplete = false;

    protected string $sgMode = self::DEFAULT_MODE;

    protected string $sgEventsFolder = self::DEFAULT_FOLDER_PATH;

    protected string $sgCalendarTitle = self::DEFAULT_FEED_TITLE;

    protected int $sgEvenstListPerPage = self::DEFAULT_EVENTS_PER_PAGE;

    protected string $sgPageTitle = self::DEFAULT_PAGE_TITLE;

    protected ?int $sgPage = null;

    protected ?int $sgArticle = null;

    protected ?int $sgContentList = null;

    protected ?int $sgCalendar = null;

    protected ?int $sgModuleReader = null;

    protected ?int $sgModuleList = null;

    protected ?int $sgModuleCalendar = null;

    protected bool $sgArchived = false;

    protected int $sgArchivedAt = 0;

    protected string $sgArchivedMode = self::DEFAULT_ARCHIVE_MODE;

    public function __clone()
    {
        foreach (get_object_vars($this) as $name => $value) {
            if (\is_object($value)) {
                $this->{$name} = clone $value;
            }
        }
    }

    public function reset(): self
    {
        $this->setSgInstallComplete(false)
            ->setSgMode(static::DEFAULT_MODE)
            ->setSgEventsFolder(self::DEFAULT_FOLDER_PATH)
            ->setSgCalendarTitle(self::DEFAULT_FEED_TITLE)
            ->setSgEventsListPerPage(self::DEFAULT_EVENTS_PER_PAGE)
            ->setSgPageTitle(self::DEFAULT_PAGE_TITLE)
            ->setSgPage(null)
            ->setSgArticle(null)
            ->setSgContentList(null)
            ->setSgCalendar(null)
            ->setSgModuleReader(null)
            ->setSgModuleList(null)
            ->setSgModuleCalendar(null)
            ->setSgArchived(false)
            ->setSgArchivedAt(0)
            ->setSgArchivedMode(self::DEFAULT_ARCHIVE_MODE)
        ;

        return $this;
    }

    public function import(\stdClass $json): self
    {
        $this->setSgInstallComplete($json->installComplete ?? false)
            ->setSgMode($json->mode ?? static::DEFAULT_MODE)
            ->setSgEventsFolder($json->events_folder ?? self::DEFAULT_FOLDER_PATH)
            ->setSgCalendarTitle($json->calendar_title ?? self::DEFAULT_FEED_TITLE)
            ->setSgEventsListPerPage($json->events_list_per_page ?? self::DEFAULT_EVENTS_PER_PAGE)
            ->setSgPageTitle($json->page_title ?? self::DEFAULT_PAGE_TITLE)
            ->setSgPage($json->contao->page ?? null)
            ->setSgArticle($json->contao->article ?? null)
            ->setSgContentList($json->contao->contents->list ?? null)
            ->setSgCalendar($json->contao->calendar ?? null)
            ->setSgModuleReader($json->contao->modules->reader ?? null)
            ->setSgModuleList($json->contao->modules->list ?? null)
            ->setSgModuleCalendar($json->contao->modules->calendar ?? null)
            ->setSgArchived($json->archived->status ?? false)
            ->setSgArchivedAt($json->archived->at ?? 0)
            ->setSgArchivedMode($json->archived->mode ?? self::DEFAULT_ARCHIVE_MODE)
        ;

        return $this;
    }

    public function export(): \stdClass
    {
        $json = new \stdClass();
        $json->installComplete = $this->getSgInstallComplete();
        $json->mode = $this->getSgMode();

        $json->events_folder = $this->getSgEventsFolder();
        $json->calendar_title = $this->getSgCalendarTitle();
        $json->events_list_per_page = $this->getSgEventsListPerPage();
        $json->page_title = $this->getSgPageTitle();

        $json->contao = new \stdClass();
        $json->contao->page = $this->getSgPage();
        $json->contao->article = $this->getSgArticle();
        $json->contao->calendar = $this->getSgCalendar();

        $json->contao->contents = new \stdClass();
        $json->contao->contents->list = $this->getSgContentList();

        $json->contao->modules = new \stdClass();
        $json->contao->modules->reader = $this->getSgModuleReader();
        $json->contao->modules->list = $this->getSgModuleList();
        $json->contao->modules->calendar = $this->getSgModuleCalendar();

        $json->archived = new \stdClass();
        $json->archived->status = $this->getSgArchived();
        $json->archived->at = $this->getSgArchivedAt();
        $json->archived->mode = $this->getSgArchivedMode();

        return $json;
    }

    public function getContaoModulesIds(): array
    {
        if (!$this->getSgInstallComplete()
        && (\in_array($this->getSgArchivedMode(), [self::ARCHIVE_MODE_EMPTY, self::ARCHIVE_MODE_DELETE], true))
        ) {
            return [];
        }

        return [
            $this->getSgModuleReader(),
            $this->getSgModuleList(),
            $this->getSgModuleCalendar(),
        ];
    }

    public function getContaoPagesIds(): array
    {
        if (!$this->getSgInstallComplete()
        && (\in_array($this->getSgArchivedMode(), [self::ARCHIVE_MODE_EMPTY, self::ARCHIVE_MODE_DELETE], true))
        ) {
            return [];
        }

        return [$this->getSgPage()];
    }

    public function getContaoContentsIds(): array
    {
        if (!$this->getSgInstallComplete()
        && (\in_array($this->getSgArchivedMode(), [self::ARCHIVE_MODE_EMPTY, self::ARCHIVE_MODE_DELETE], true))
        ) {
            return [];
        }

        return [$this->getSgContentList()];
    }

    public function getContaoArticlesIds(): array
    {
        if (!$this->getSgInstallComplete()
        && (\in_array($this->getSgArchivedMode(), [self::ARCHIVE_MODE_EMPTY, self::ARCHIVE_MODE_DELETE], true))
        ) {
            return [];
        }

        return [$this->getSgArticle()];
    }

    public function getContaoFoldersIds(): array
    {
        if (!$this->getSgInstallComplete()
        && (\in_array($this->getSgArchivedMode(), [self::ARCHIVE_MODE_EMPTY, self::ARCHIVE_MODE_DELETE], true))
        ) {
            return [];
        }

        return [$this->getSgEventsFolder()];
    }

    public function getContaoUsersIds(): array
    {
        return [];
    }

    public function getContaoUserGroupsIds(): array
    {
        return [];
    }

    public function getContaoMembersIds(): array
    {
        return [];
    }

    public function getContaoMemberGroupsIds(): array
    {
        return [];
    }

    public function resetContaoModulesIds(): void
    {
        $this->setSgModuleCalendar(null);
        $this->setSgModuleList(null);
        $this->setSgModuleReader(null);
    }

    public function resetContaoPagesIds(): void
    {
        $this->setSgPage(null);
    }

    public function resetContaoContentsIds(): void
    {
        $this->setSgContentList(null);
    }

    public function resetContaoArticlesIds(): void
    {
        $this->setSgArticle(null);
    }

    public function resetContaoFoldersIds(): void
    {
        $this->setSgEventsFolder('');
    }

    public function resetContaoUsersIds(): void
    {
    }

    public function resetContaoUserGroupsIds(): void
    {
    }

    public function resetContaoMembersIds(): void
    {
    }

    public function resetContaoMemberGroupsIds(): void
    {
    }

    public function getSgInstallComplete(): bool
    {
        return $this->sgInstallComplete;
    }

    /**
     * @param mixed $sgInstallComplete
     */
    public function setSgInstallComplete(bool $sgInstallComplete): self
    {
        $this->sgInstallComplete = $sgInstallComplete;

        return $this;
    }

    public function getSgMode(): string
    {
        return $this->sgMode;
    }

    public function setSgMode(string $sgMode): self
    {
        if (!\in_array($sgMode, static::MODES_ALLOWED, true)) {
            throw new \InvalidArgumentException(sprintf('Invalid mode "%s" given', $sgMode));
        }

        $this->sgMode = $sgMode;

        return $this;
    }

    public function getSgEventsFolder(): string
    {
        return $this->sgEventsFolder;
    }

    public function setSgEventsFolder(string $sgEventsFolder): self
    {
        $this->sgEventsFolder = $sgEventsFolder;

        return $this;
    }

    public function getSgCalendarTitle(): string
    {
        return $this->sgCalendarTitle;
    }

    public function setSgCalendarTitle(string $sgCalendarTitle): self
    {
        $this->sgCalendarTitle = $sgCalendarTitle;

        return $this;
    }

    public function getSgEventsListPerPage(): int
    {
        return $this->sgEvenstListPerPage;
    }

    public function setSgEventsListPerPage(int $sgEvenstListPerPage): self
    {
        $this->sgEvenstListPerPage = $sgEvenstListPerPage;

        return $this;
    }

    public function getSgPageTitle(): string
    {
        return $this->sgPageTitle;
    }

    public function setSgPageTitle(string $sgPageTitle): self
    {
        $this->sgPageTitle = $sgPageTitle;

        return $this;
    }

    public function getSgPage(): ?int
    {
        return $this->sgPage;
    }

    public function setSgPage(?int $sgPage): self
    {
        $this->sgPage = $sgPage;

        return $this;
    }

    public function getSgCalendar(): ?int
    {
        return $this->sgCalendar;
    }

    public function setSgCalendar(?int $sgCalendar): self
    {
        $this->sgCalendar = $sgCalendar;

        return $this;
    }

    public function getSgModuleReader(): ?int
    {
        return $this->sgModuleReader;
    }

    public function setSgModuleReader(?int $sgModuleReader): self
    {
        $this->sgModuleReader = $sgModuleReader;

        return $this;
    }

    public function getSgModuleList(): ?int
    {
        return $this->sgModuleList;
    }

    public function setSgModuleList(?int $sgModuleList): self
    {
        $this->sgModuleList = $sgModuleList;

        return $this;
    }

    public function getSgModuleCalendar(): ?int
    {
        return $this->sgModuleCalendar;
    }

    public function setSgModuleCalendar(?int $sgModuleCalendar): self
    {
        $this->sgModuleCalendar = $sgModuleCalendar;

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

    public function getSgArticle(): ?int
    {
        return $this->sgArticle;
    }

    public function setSgArticle(?int $sgArticle): self
    {
        $this->sgArticle = $sgArticle;

        return $this;
    }

    public function getSgContentList(): ?int
    {
        return $this->sgContentList;
    }

    public function setSgContentList(?int $sgContentList): self
    {
        $this->sgContentList = $sgContentList;

        return $this;
    }
}
