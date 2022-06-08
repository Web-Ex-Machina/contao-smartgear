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

namespace WEM\SmartgearBundle\Config;

use WEM\SmartgearBundle\Classes\Config\ConfigJsonInterface;

class Framway implements ConfigJsonInterface
{
    public const USE_FA_NONE = false;
    public const DEFAULT_USE_FA = self::USE_FA_NONE;
    public const DEFAULT_USE_TOASTR = true;
    public const DEFAULT_USE_OUTDATED_BROWSER = true;
    public const DEFAULT_USE_TARTE_AU_CITRON = true;
    public const DEFAULT_DEBUG = false;
    /** @var \stdClass */
    protected $originalConfig;
    /** @var array */
    protected $themes = [];
    /** @var array */
    protected $themesAvailables = [];
    /** @var array */
    protected $components = [];
    /** @var array */
    protected $componentsAvailables = [];
    /** @var array */
    protected $colors = [];
    /** @var string|bool */
    protected $useFA = self::DEFAULT_USE_FA;
    /** @var bool */
    protected $useToastr = self::DEFAULT_USE_TOASTR;
    /** @var bool */
    protected $useOutdatebrowser = self::DEFAULT_USE_OUTDATED_BROWSER;
    /** @var bool */
    protected $useTarteaucitron = self::DEFAULT_USE_TARTE_AU_CITRON;
    /** @var bool */
    protected $debug = self::DEFAULT_DEBUG;
    /** @var ?string */
    protected $primary;
    /** @var ?string */
    protected $secondary;
    /** @var ?string */
    protected $tertiary;
    /** @var ?string */
    protected $success;
    /** @var ?string */
    protected $info;
    /** @var ?string */
    protected $warning;
    /** @var ?string */
    protected $error;

    public function reset(): self
    {
        $this->setThemes([])
            ->setThemesAvailables([])
            ->setComponents([])
            ->setComponentsAvailables([])
            ->setColors([])
            ->setPrimary(null)
            ->setSecondary(null)
            ->setTertiary(null)
            ->setSuccess(null)
            ->setInfo(null)
            ->setWarning(null)
            ->setError(null)
            ->setUseFA(self::DEFAULT_USE_FA)
            ->setUseToastr(self::DEFAULT_USE_TOASTR)
            ->setUseOutdatebrowser(self::DEFAULT_USE_OUTDATED_BROWSER)
            ->setUseTarteaucitron(self::DEFAULT_USE_TARTE_AU_CITRON)
            ->setDebug(self::DEFAULT_DEBUG)
        ;

        return $this;
    }

    public function import(\stdClass $json): self
    {
        $this->setOriginalConfig($json)
            ->setThemes($json->themes ?? [])
            ->setThemesAvailables(
                property_exists($json, 'themesAvailables')
                ? $json->themesAvailables
                : $json->themes ?? []
            )
            ->setComponents($json->components ?? [])
            ->setComponentsAvailables(
                property_exists($json, 'componentsAvailables')
                ? $json->componentsAvailables
                : $json->components ?? []
            )
            ->setColors($json->colors ?? [])
            ->setPrimary($json->primary ?? null)
            ->setSecondary($json->secondary ?? null)
            ->setTertiary($json->tertiary ?? null)
            ->setSuccess($json->success ?? null)
            ->setInfo($json->info ?? null)
            ->setWarning($json->warning ?? null)
            ->setError($json->error ?? null)
            ->setUseFA(self::DEFAULT_USE_FA)
            ->setUseToastr($json->useToastr ?? self::DEFAULT_USE_TOASTR)
            ->setUseOutdatebrowser($json->useOutdatebrowser ?? self::DEFAULT_USE_OUTDATED_BROWSER)
            ->setUseTarteaucitron($json->useTarteaucitron ?? self::DEFAULT_USE_TARTE_AU_CITRON)
            ->setDebug($json->debug ?? self::DEFAULT_DEBUG)
        ;

        return $this;
    }

    public function export(): string
    {
        $json = $this->getOriginalConfig();

        $json->themes = $this->getThemes();
        $json->themesAvailables = $this->getThemesAvailables();
        $json->components = $this->getComponents();
        $json->componentsAvailables = $this->getComponentsAvailables();
        $json->useFA = $this->getUseFA();
        $json->useToastr = $this->getUseToastr();
        $json->useOutdatebrowser = $this->getUseOutdatebrowser();
        $json->useTarteaucitron = $this->getUseTarteaucitron();
        $json->debug = $this->getDebug();
        $json->colors = $this->getColors();
        if (null !== $this->getPrimary()) {
            $json->primary = $this->getPrimary();
        }
        if (null !== $this->getSecondary()) {
            $json->secondary = $this->getSecondary();
        }
        if (null !== $this->getTertiary()) {
            $json->tertiary = $this->getTertiary();
        }
        if (null !== $this->getSuccess()) {
            $json->success = $this->getSuccess();
        }
        if (null !== $this->getInfo()) {
            $json->info = $this->getInfo();
        }
        if (null !== $this->getWarning()) {
            $json->warning = $this->getWarning();
        }
        if (null !== $this->getError()) {
            $json->error = $this->getError();
        }

        return json_encode($json, \JSON_PRETTY_PRINT);
    }

    public function getDebug(): bool
    {
        return $this->debug;
    }

    public function setDebug(bool $debug): self
    {
        $this->debug = $debug;

        return $this;
    }

    public function getThemes(): array
    {
        return $this->themes;
    }

    public function setThemes(array $themes): self
    {
        $this->themes = $themes;

        return $this;
    }

    public function getComponents(): array
    {
        return $this->components;
    }

    public function setComponents(array $components): self
    {
        $this->components = $components;

        return $this;
    }

    public function getUseFA()
    {
        return $this->useFA;
    }

    public function setUseFA($useFA): self
    {
        if (static::USE_FA_NONE !== $useFA) {
            throw new \InvalidArgumentException(sprintf('Invalid useFA "%s" given', $useFA));
        }
        $this->useFA = $useFA;

        return $this;
    }

    public function getUseToastr(): bool
    {
        return $this->useToastr;
    }

    public function setUseToastr(bool $useToastr): self
    {
        $this->useToastr = $useToastr;

        return $this;
    }

    public function getUseOutdatebrowser(): bool
    {
        return $this->useOutdatebrowser;
    }

    public function setUseOutdatebrowser(bool $useOutdatebrowser): self
    {
        $this->useOutdatebrowser = $useOutdatebrowser;

        return $this;
    }

    public function getUseTarteaucitron(): bool
    {
        return $this->useTarteaucitron;
    }

    public function setUseTarteaucitron(bool $useTarteaucitron): self
    {
        $this->useTarteaucitron = $useTarteaucitron;

        return $this;
    }

    public function getColors(): array
    {
        return $this->colors;
    }

    public function setColors(array $colors): self
    {
        $this->colors = $colors;

        return $this;
    }

    public function getOriginalConfig(): \stdClass
    {
        return $this->originalConfig;
    }

    public function getPrimary(): ?string
    {
        return $this->primary;
    }

    public function setPrimary(?string $primary): self
    {
        $this->primary = $primary;

        return $this;
    }

    public function getSecondary(): ?string
    {
        return $this->secondary;
    }

    public function setSecondary(?string $secondary): self
    {
        $this->secondary = $secondary;

        return $this;
    }

    public function getTertiary(): ?string
    {
        return $this->tertiary;
    }

    public function setTertiary(?string $tertiary): self
    {
        $this->tertiary = $tertiary;

        return $this;
    }

    public function getSuccess(): ?string
    {
        return $this->success;
    }

    public function setSuccess(?string $success): self
    {
        $this->success = $success;

        return $this;
    }

    public function getInfo(): ?string
    {
        return $this->info;
    }

    public function setInfo(?string $info): self
    {
        $this->info = $info;

        return $this;
    }

    public function getWarning(): ?string
    {
        return $this->warning;
    }

    public function setWarning(?string $warning): self
    {
        $this->warning = $warning;

        return $this;
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    public function setError(?string $error): self
    {
        $this->error = $error;

        return $this;
    }

    public function getThemesAvailables(): array
    {
        return $this->themesAvailables;
    }

    public function setThemesAvailables(array $themesAvailables): self
    {
        $this->themesAvailables = $themesAvailables;

        return $this;
    }

    public function getComponentsAvailables(): array
    {
        return $this->componentsAvailables;
    }

    public function setComponentsAvailables(array $componentsAvailables): self
    {
        $this->componentsAvailables = $componentsAvailables;

        return $this;
    }

    protected function setOriginalConfig(\stdClass $originalConfig): self
    {
        $this->originalConfig = $originalConfig;

        return $this;
    }
}
