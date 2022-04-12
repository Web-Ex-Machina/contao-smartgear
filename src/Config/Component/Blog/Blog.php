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

use WEM\SmartgearBundle\Classes\Config\ConfigModuleInterface;

class Blog implements ConfigModuleInterface
{
    public const MODE_SIMPLE = 'simple';
    public const MODE_EXPERT = 'expert';
    public const MODES_ALLOWED = [
        self::MODE_SIMPLE,
        self::MODE_EXPERT,
    ];

    public const DEFAULT_MODE = self::MODE_SIMPLE;
    /** @var bool */
    protected $sgInstallComplete = false;
    /** @var string */
    protected $sgMode = self::DEFAULT_MODE;
    /** @var array */
    protected $sgNewsArchives = [];

    public function reset(): self
    {
        $this->setSgInstallComplete(false)
            ->setSgMode(static::DEFAULT_MODE)
            ->setSgNewsArchives([])
        ;

        return $this;
    }

    public function import(\stdClass $json): self
    {
        $this->setSgInstallComplete($json->installComplete ?? false)
            ->setSgMode($json->mode ?? static::DEFAULT_MODE)
        ;

        foreach ($json->news_archives as $newsArchiveJson) {
            $this->addOrUpdateNewsArchive((new NewsArchive())->import($newsArchiveJson));
        }

        return $this;
    }

    public function export(): \stdClass
    {
        $json = new \stdClass();
        $json->installComplete = $this->getSgInstallComplete();
        $json->mode = $this->getSgMode();
        $json->news_archives = [];
        foreach ($this->getSgNewsArchives() as $newsArchiveConfig) {
            $json->news_archives[] = $newsArchiveConfig->export();
        }

        return $json;
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

    public function getSgInstallComplete(): bool
    {
        return $this->sgInstallComplete;
    }

    public function setSgInstallComplete(bool $sgInstallComplete): self
    {
        $this->sgInstallComplete = $sgInstallComplete;

        return $this;
    }

    public function getSgNewsArchives(): array
    {
        return $this->sgNewsArchives;
    }

    public function setSgNewsArchives(array $sgNewsArchives): self
    {
        $this->sgNewsArchives = $sgNewsArchives;

        return $this;
    }

    public function addOrUpdateNewsArchive(NewsArchive $newsArchive): self
    {
        $found = false;
        /* @var NewsArchive */
        foreach ($this->sgNewsArchives as $index => $existingNewsArchive) {
            if ($existingNewsArchive->getSgNewsArchive() === $newsArchive->getSgNewsArchive()) {
                $this->sgNewsArchives[$index] = $newsArchive;
                $found = true;
            }
        }
        if (!$found) {
            $this->sgNewsArchives[] = $newsArchive;
        }

        return $this;
    }

    /**
     * Retrieve a news configuration by its id.
     *
     * @param int $id [description]
     *
     * @return NewsArchive|null return null if no matching configuration found
     */
    public function getNewsArchiveById(int $id): ?NewsArchive
    {
        foreach ($this->sgNewsArchives as $newsArchive) {
            if ($id === $newsArchive->getSgNewsArchive()) {
                return $newsArchive;
            }
        }

        return null;
    }
}
