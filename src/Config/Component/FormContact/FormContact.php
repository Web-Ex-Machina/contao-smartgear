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

namespace WEM\SmartgearBundle\Config\Component\FormContact;

use WEM\SmartgearBundle\Classes\Config\ConfigModuleInterface;

class FormContact implements ConfigModuleInterface
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
    protected $sgFormContactFolder = self::DEFAULT_FOLDER_PATH;
    /** @var string */
    protected $sgFormContactTitle = self::DEFAULT_FEED_TITLE;
    /** @var string */
    protected $sgPageTitle = self::DEFAULT_PAGE_TITLE;
    /** @var int */
    protected $sgPage;
    /** @var int */
    protected $sgArticle;
    /** @var int */
    protected $sgContent;
    /** @var int */
    protected $sgModuleFormContact;
    /** @var int */
    protected $sgFormContactCategory;
    /** @var bool */
    protected $sgArchived = false;
    /** @var int */
    protected $sgArchivedAt = 0;
    /** @var string */
    protected $sgArchivedMode = self::DEFAULT_ARCHIVE_MODE;

    public function reset(): self
    {
        $this->setSgInstallComplete(false)
            ->setSgFormContactFolder(self::DEFAULT_FOLDER_PATH)
            ->setSgFormContactTitle(self::DEFAULT_FEED_TITLE)
            ->setSgPageTitle(self::DEFAULT_PAGE_TITLE)
            ->setSgPage(null)
            ->setSgArticle(null)
            ->setSgContent(null)
            ->setSgModuleFormContact(null)
            ->setSgArchived(false)
            ->setSgArchivedAt(0)
            ->setSgArchivedMode(self::DEFAULT_ARCHIVE_MODE)
        ;

        return $this;
    }

    public function import(\stdClass $json): self
    {
        $this->setSgInstallComplete($json->installComplete ?? false)
            ->setSgFormContactFolder($json->faq_folder ?? self::DEFAULT_FOLDER_PATH)
            ->setSgFormContactTitle($json->faq_title ?? self::DEFAULT_FEED_TITLE)
            ->setSgPageTitle($json->page_title ?? self::DEFAULT_PAGE_TITLE)
            ->setSgPage($json->contao->page ?? null)
            ->setSgArticle($json->contao->article ?? null)
            ->setSgContent($json->contao->content ?? null)
            ->setSgModuleFormContact($json->contao->modules->faq ?? null)
            ->setSgFormContactCategory($json->contao->faqCategory ?? null)
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

        $json->faq_folder = $this->getSgFormContactFolder();
        $json->faq_title = $this->getSgFormContactTitle();
        $json->page_title = $this->getSgPageTitle();

        $json->contao = new \stdClass();
        $json->contao->page = $this->getSgPage();
        $json->contao->article = $this->getSgArticle();
        $json->contao->content = $this->getSgContent();
        $json->contao->faqCategory = $this->getSgFormContactCategory();

        $json->contao->modules = new \stdClass();
        $json->contao->modules->faq = $this->getSgModuleFormContact();

        $json->archived = new \stdClass();
        $json->archived->status = $this->getSgArchived();
        $json->archived->at = $this->getSgArchivedAt();
        $json->archived->mode = $this->getSgArchivedMode();

        return $json;
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

    public function getSgFormContactFolder(): string
    {
        return $this->sgFormContactFolder;
    }

    public function setSgFormContactFolder(string $sgFormContactFolder): self
    {
        $this->sgFormContactFolder = $sgFormContactFolder;

        return $this;
    }

    public function getSgFormContactTitle(): string
    {
        return $this->sgFormContactTitle;
    }

    public function setSgFormContactTitle(string $sgFormContactTitle): self
    {
        $this->sgFormContactTitle = $sgFormContactTitle;

        return $this;
    }

    public function getSgFormContactListPerPage(): int
    {
        return $this->sgEvenstListPerPage;
    }

    public function setSgFormContactListPerPage(int $sgEvenstListPerPage): self
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

    public function getSgModuleFormContact(): ?int
    {
        return $this->sgModuleFormContact;
    }

    public function setSgModuleFormContact(?int $sgModuleFormContact): self
    {
        $this->sgModuleFormContact = $sgModuleFormContact;

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

    public function getSgFormContactCategory(): ?int
    {
        return $this->sgFormContactCategory;
    }

    public function setSgFormContactCategory(?int $sgFormContactCategory): self
    {
        $this->sgFormContactCategory = $sgFormContactCategory;

        return $this;
    }
}
