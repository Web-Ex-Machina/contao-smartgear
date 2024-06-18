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

namespace WEM\SmartgearBundle\Config\Component\Blog;

use InvalidArgumentException;
use WEM\SmartgearBundle\Classes\Config\ConfigModuleInterface;

class Blog implements ConfigModuleInterface
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

    public const DEFAULT_MODE = self::MODE_SIMPLE;

    public const DEFAULT_ARCHIVE_MODE = self::ARCHIVE_MODE_EMPTY;

    protected bool $sgInstallComplete = false;

    protected string $sgMode = self::DEFAULT_MODE;

    protected ?int $sgNewsArchive = null;

    protected ?int $sgPage = null;

    protected ?int $sgArticle = null;

    protected ?int $sgContentList = null;

    protected ?int $sgModuleReader = null;

    protected ?int $sgModuleList = null;

    protected array $sgPresets = [];

    protected ?int $sgCurrentPresetIndex = null;

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
            ->setSgPage(null)
            ->setSgArticle(null)
            ->setSgContentList(null)
            ->setSgModuleReader(null)
            ->setSgModuleList(null)
            ->setSgNewsArchive(null)
            ->setSgPresets([])
            ->setSgCurrentPresetIndex(null)
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
            ->setSgPage($json->contao->page ?? null)
            ->setSgArticle($json->contao->article ?? null)
            ->setSgContentList($json->contao->contents->list ?? null)
            ->setSgModuleReader($json->contao->modules->reader ?? null)
            ->setSgModuleList($json->contao->modules->list ?? null)
            ->setSgNewsArchive($json->contao->archive ?? null)
            ->setSgArchived($json->archived->status ?? false)
            ->setSgArchivedAt($json->archived->at ?? 0)
            ->setSgArchivedMode($json->archived->mode ?? self::DEFAULT_ARCHIVE_MODE)
        ;

        foreach ($json->presets as $presetJson) {
            $this->addOrUpdatePreset((new Preset())->import($presetJson));
        }

        $this->setSgCurrentPresetIndex($json->currentPresetIndex ?? null);

        return $this;
    }

    public function export(): \stdClass
    {
        $json = new \stdClass();
        $json->installComplete = $this->getSgInstallComplete();
        $json->mode = $this->getSgMode();
        $json->currentPresetIndex = $this->getSgCurrentPresetIndex();
        $json->presets = [];
        foreach ($this->getSgPresets() as $presetConfig) {
            $json->presets[] = $presetConfig->export();
        }

        $json->contao = new \stdClass();
        $json->contao->page = $this->getSgPage();
        $json->contao->article = $this->getSgArticle();
        $json->contao->archive = $this->getSgNewsArchive();

        $json->contao->contents = new \stdClass();
        $json->contao->contents->list = $this->getSgContentList();

        $json->contao->modules = new \stdClass();
        $json->contao->modules->reader = $this->getSgModuleReader();
        $json->contao->modules->list = $this->getSgModuleList();

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

        return [
            $this->getSgContentList(),
        ];
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

    public function resetContaoModulesIds(): void
    {
        $this->setSgModuleList(null);
        $this->setSgModuleReader(null);
    }

    public function resetContaoPagesIds(): void
    {
        $this->setSgPage(null);
    }

    public function resetContaoContentsIds(): void
    {
        $this->setSgContentHeadline(null); //TODO : setSgContentHeadline not found
        $this->setSgContentList(null);
    }

    public function resetContaoArticlesIds(): void
    {
        $this->setSgArticle(null);
    }

    public function resetContaoFoldersIds(): void
    {
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

    public function getSgNewsArchive(): ?int
    {
        return $this->sgNewsArchive;
    }

    public function setSgNewsArchive(?int $sgNewsArchive): self
    {
        $this->sgNewsArchive = $sgNewsArchive;

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

    public function getSgPresets(): array
    {
        return $this->sgPresets;
    }

    public function setSgPresets(array $sgPresets): self
    {
        $this->sgPresets = $sgPresets;

        return $this;
    }

    public function addOrUpdatePreset(Preset $preset, ?int $index = null): self
    {
        if (null === $index) {
            $this->sgPresets[] = $preset;
        } else {
            $this->sgPresets[$index] = $preset;
        }

        if (1 === \count($this->sgPresets)
        && null === $this->sgCurrentPresetIndex
        ) {
            $this->sgCurrentPresetIndex = 0;
        }

        return $this;
    }

    public function getPresetIndex(Preset $presetToFind): ?int
    {
        foreach ($this->getSgPresets() as $index => $preset) {
            if ($preset === $presetToFind) {
                return $index;
            }
        }

        return null;
    }

    /**
     * Retrieve a news configuration by its id.
     *
     * @return Preset|null return null if no matching configuration found
     */
    public function getPresetByIndex(int $index): ?Preset
    {
        return $this->sgPresets[$index] ?? null;
    }

    public function getSgCurrentPresetIndex(): ?int
    {
        return $this->sgCurrentPresetIndex;
    }

    public function setSgCurrentPresetIndex(?int $sgCurrentPresetIndex): self
    {
        if (null !== $sgCurrentPresetIndex
        && !$this->getPresetByIndex($sgCurrentPresetIndex) instanceof Preset) {
            throw new InvalidArgumentException('The provided preset ID does not refer to any known preset configuration.');
        }

        $this->sgCurrentPresetIndex = $sgCurrentPresetIndex;

        return $this;
    }

    public function getCurrentPreset(): ?Preset
    {
        $presetIndex = $this->getSgCurrentPresetIndex();

        return null !== $presetIndex ? $this->getPresetByIndex($presetIndex) : null;
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
