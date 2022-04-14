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
    public const DEFAULT_MODE = self::MODE_SIMPLE;

    /** @var bool */
    protected $sgInstallComplete = false;
    /** @var string */
    protected $sgMode = self::DEFAULT_MODE;
    /** @var int */
    protected $sgNewsArchive;
    /** @var int */
    protected $sgPage;
    /** @var int */
    protected $sgModuleReader;
    /** @var int */
    protected $sgModuleList;
    /** @var array */
    protected $sgPresets = [];
    /** @var int */
    protected $sgCurrentPresetIndex;

    public function reset(): self
    {
        $this->setSgInstallComplete(false)
            ->setSgMode(static::DEFAULT_MODE)
            ->setSgPage(null)
            ->setSgModuleReader(null)
            ->setSgModuleList(null)
            ->setSgNewsArchive(null)
            ->setSgPresets([])
            ->setSgCurrentPresetIndex(null)
        ;

        return $this;
    }

    public function import(\stdClass $json): self
    {
        $this->setSgInstallComplete($json->installComplete ?? false)
            ->setSgMode($json->mode ?? static::DEFAULT_MODE)
            ->setSgPage($json->page ?? null)
            ->setSgModuleReader($json->moduleReader ?? null)
            ->setSgModuleList($json->moduleList ?? null)
            ->setSgNewsArchive($json->archive ?? null)
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
        $json->page = $this->getSgPage();
        $json->moduleReader = $this->getSgModuleReader();
        $json->moduleList = $this->getSgModuleList();
        $json->archive = $this->getSgNewsArchive();
        $json->currentPresetIndex = $this->getSgCurrentPresetIndex();
        $json->presets = [];
        foreach ($this->getSgPresets() as $presetConfig) {
            $json->presets[] = $presetConfig->export();
        }

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

    /**
     * @return mixed
     */
    public function getSgPage(): ?int
    {
        return $this->sgPage;
    }

    /**
     * @param mixed $sgPage
     */
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

        if (1 === count($this->sgPresets)
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
        && null === $this->getPresetByIndex($sgCurrentPresetIndex)) {
            dump($sgCurrentPresetIndex);
            dump($this->sgPresets);
            throw new InvalidArgumentException('The provided preset ID does not refer to any known preset configuration.');
        }
        $this->sgCurrentPresetIndex = $sgCurrentPresetIndex;

        return $this;
    }

    public function getCurrentPreset(): ?Preset
    {
        return $this->getPresetByIndex($this->getSgCurrentPresetIndex());
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
}
