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

class FramwayCombined implements ConfigJsonInterface
{
    protected \stdClass $originalConfig;

    protected array $colors = [];

    protected ?string $primary = null;

    protected ?string $secondary = null;

    protected ?string $tertiary = null;

    protected ?string $success = null;

    protected ?string $info = null;

    protected ?string $warning = null;

    protected ?string $error = null;

    public function reset(): self
    {
        $this->setColors([])
            ->setPrimary(null)
            ->setSecondary(null)
            ->setTertiary(null)
            ->setSuccess(null)
            ->setInfo(null)
            ->setWarning(null)
            ->setError(null)
        ;

        return $this;
    }

    public function import(\stdClass $json): self
    {
        $this->setOriginalConfig($json)
        ;

        if ($json->colors) {
            $this->setColors(json_decode(json_encode($json->colors), true));
        }

        $this->setPrimary($json->primary ?? null)
            ->setSecondary($json->secondary ?? null)
            ->setTertiary($json->tertiary ?? null)
            ->setSuccess($json->success ?? null)
            ->setInfo($json->info ?? null)
            ->setWarning($json->warning ?? null)
            ->setError($json->error ?? null)
        ;

        return $this;
    }

    public function export(): string
    {
        $json = $this->getOriginalConfig();

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

    public function getColors(): array
    {
        return $this->colors;
    }

    public function setColors(array $colors): self
    {
        $this->colors = $colors;

        return $this;
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
