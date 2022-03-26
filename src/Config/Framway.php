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
    /** @var array [description] */
    protected $themes = [];
    /** @var array [description] */
    protected $components = [];
    /** @var array [description] */
    protected $colors = [];
    /** @var mixed string|bool */
    protected $useFA = self::DEFAULT_USE_FA;
    /** @var bool */
    protected $useToastr = self::DEFAULT_USE_TOASTR;
    /** @var bool */
    protected $useOutdatebrowser = self::DEFAULT_USE_OUTDATED_BROWSER;
    /** @var bool */
    protected $useTarteaucitron = self::DEFAULT_USE_TARTE_AU_CITRON;
    /** @var bool */
    protected $debug = self::DEFAULT_DEBUG;

    public function reset(): self
    {
        $this->setThemes([])
            ->setComponents([])
            ->setColors([])
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
            ->setComponents($json->components ?? [])
            ->setColors($json->colors ?? [])
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
        $json->components = $this->getComponents();
        $json->useFA = $this->getUseFA();
        $json->useToastr = $this->getUseToastr();
        $json->useOutdatebrowser = $this->getUseOutdatebrowser();
        $json->useTarteaucitron = $this->getUseTarteaucitron();
        $json->debug = $this->getDebug();
        $json->colors = $this->getColors();

        return json_encode($json, \JSON_PRETTY_PRINT);
    }

    public function getDebug(): bool
    {
        return $this->debug;
    }

    /**
     * @param mixed $debug
     */
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

    protected function setOriginalConfig(\stdClass $originalConfig): self
    {
        $this->originalConfig = $originalConfig;

        return $this;
    }
}
