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

namespace WEM\SmartgearBundle\Config\Module\FormDataManager;

use WEM\SmartgearBundle\Classes\Config\ConfigModuleInterface;

class FormDataManager implements ConfigModuleInterface
{
    public const ARCHIVE_MODE_EMPTY = '';

    public const ARCHIVE_MODE_DELETE = 'delete';

    public const ARCHIVE_MODES_ALLOWED = [
        self::ARCHIVE_MODE_EMPTY,
        self::ARCHIVE_MODE_DELETE,
    ];

    public const DEFAULT_ARCHIVE_MODE = self::ARCHIVE_MODE_EMPTY;

    protected bool $sgInstallComplete = false;

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
            ->setSgArchived(false)
            ->setSgArchivedAt(0)
            ->setSgArchivedMode(self::DEFAULT_ARCHIVE_MODE)
        ;

        return $this;
    }

    public function import(\stdClass $json): self
    {
        $this->setSgInstallComplete($json->installComplete ?? false)
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

        $json->archived = new \stdClass();
        $json->archived->status = $this->getSgArchived();
        $json->archived->at = $this->getSgArchivedAt();
        $json->archived->mode = $this->getSgArchivedMode();

        return $json;
    }

    public function getContaoModulesIds(): array
    {
        return [];
    }

    public function getContaoPagesIds(): array
    {
        return [];
    }

    public function getContaoContentsIds(): array
    {
        return [];
    }

    public function getContaoArticlesIds(): array
    {
        return [];
    }

    public function getContaoFoldersIds(): array
    {
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
    }

    public function resetContaoPagesIds(): void
    {
    }

    public function resetContaoContentsIds(): void
    {
    }

    public function resetContaoArticlesIds(): void
    {
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
