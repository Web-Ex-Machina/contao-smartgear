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

namespace WEM\SmartgearBundle\Config\Component\Faq;

use WEM\SmartgearBundle\Classes\Config\ConfigModuleInterface;

class Faq implements ConfigModuleInterface
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
    public const DEFAULT_FOLDER_PATH = 'files/faq';
    public const DEFAULT_PAGE_TITLE = 'FAQ';
    public const DEFAULT_FEED_TITLE = 'FAQ';
    public const DEFAULT_ARCHIVE_MODE = self::ARCHIVE_MODE_EMPTY;

    /** @var bool */
    protected $sgInstallComplete = false;
    /** @var string */
    protected $sgFaqFolder = self::DEFAULT_FOLDER_PATH;
    /** @var string */
    protected $sgFaqTitle = self::DEFAULT_FEED_TITLE;
    /** @var string */
    protected $sgPageTitle = self::DEFAULT_PAGE_TITLE;
    /** @var int */
    protected $sgPage;
    /** @var int */
    protected $sgArticle;
    /** @var int */
    protected $sgContent;
    /** @var int */
    protected $sgModuleFaq;
    /** @var int */
    protected $sgFaqCategory;
    /** @var bool */
    protected $sgArchived = false;
    /** @var int */
    protected $sgArchivedAt = 0;
    /** @var string */
    protected $sgArchivedMode = self::DEFAULT_ARCHIVE_MODE;

    public function reset(): self
    {
        $this->setSgInstallComplete(false)
            ->setSgFaqFolder(self::DEFAULT_FOLDER_PATH)
            ->setSgFaqTitle(self::DEFAULT_FEED_TITLE)
            ->setSgPageTitle(self::DEFAULT_PAGE_TITLE)
            ->setSgPage(null)
            ->setSgArticle(null)
            ->setSgContent(null)
            ->setSgModuleFaq(null)
            ->setSgArchived(false)
            ->setSgArchivedAt(0)
            ->setSgArchivedMode(self::DEFAULT_ARCHIVE_MODE)
        ;

        return $this;
    }

    public function import(\stdClass $json): self
    {
        $this->setSgInstallComplete($json->installComplete ?? false)
            ->setSgFaqFolder($json->faq_folder ?? self::DEFAULT_FOLDER_PATH)
            ->setSgFaqTitle($json->faq_title ?? self::DEFAULT_FEED_TITLE)
            ->setSgPageTitle($json->page_title ?? self::DEFAULT_PAGE_TITLE)
            ->setSgPage($json->contao->page ?? null)
            ->setSgArticle($json->contao->article ?? null)
            ->setSgContent($json->contao->content ?? null)
            ->setSgModuleFaq($json->contao->modules->faq ?? null)
            ->setSgFaqCategory($json->contao->faqCategory ?? null)
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

        $json->faq_folder = $this->getSgFaqFolder();
        $json->faq_title = $this->getSgFaqTitle();
        $json->page_title = $this->getSgPageTitle();

        $json->contao = new \stdClass();
        $json->contao->page = $this->getSgPage();
        $json->contao->article = $this->getSgArticle();
        $json->contao->content = $this->getSgContent();
        $json->contao->faqCategory = $this->getSgFaqCategory();

        $json->contao->modules = new \stdClass();
        $json->contao->modules->faq = $this->getSgModuleFaq();

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

        return [$this->getSgModuleFaq()];
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

        return [$this->getSgArticle()];
    }

    public function getContaoArticlesIds(): array
    {
        if (!$this->getSgInstallComplete()
        && (\in_array($this->getSgArchivedMode(), [self::ARCHIVE_MODE_EMPTY, self::ARCHIVE_MODE_DELETE], true))
        ) {
            return [$this->getSgContent()];
        }

        return [];
    }

    public function getContaoFoldersIds(): array
    {
        if (!$this->getSgInstallComplete()
        && (\in_array($this->getSgArchivedMode(), [self::ARCHIVE_MODE_EMPTY, self::ARCHIVE_MODE_DELETE], true))
        ) {
            return [];
        }

        return [];
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

    public function getSgInstallComplete(): bool
    {
        return $this->sgInstallComplete;
    }

    public function setSgInstallComplete(bool $sgInstallComplete): self
    {
        $this->sgInstallComplete = $sgInstallComplete;

        return $this;
    }

    public function getSgFaqFolder(): string
    {
        return $this->sgFaqFolder;
    }

    public function setSgFaqFolder(string $sgFaqFolder): self
    {
        $this->sgFaqFolder = $sgFaqFolder;

        return $this;
    }

    public function getSgFaqTitle(): string
    {
        return $this->sgFaqTitle;
    }

    public function setSgFaqTitle(string $sgFaqTitle): self
    {
        $this->sgFaqTitle = $sgFaqTitle;

        return $this;
    }

    public function getSgFaqListPerPage(): int
    {
        return $this->sgEvenstListPerPage;
    }

    public function setSgFaqListPerPage(int $sgEvenstListPerPage): self
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

    public function getSgModuleFaq(): ?int
    {
        return $this->sgModuleFaq;
    }

    public function setSgModuleFaq(?int $sgModuleFaq): self
    {
        $this->sgModuleFaq = $sgModuleFaq;

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

    public function getSgContent(): ?int
    {
        return $this->sgContent;
    }

    public function setSgContent(?int $sgContent): self
    {
        $this->sgContent = $sgContent;

        return $this;
    }

    public function getSgFaqCategory(): ?int
    {
        return $this->sgFaqCategory;
    }

    public function setSgFaqCategory(?int $sgFaqCategory): self
    {
        $this->sgFaqCategory = $sgFaqCategory;

        return $this;
    }
}
