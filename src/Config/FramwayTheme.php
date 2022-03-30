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

class FramwayTheme implements ConfigJsonInterface
{
    /** @var \stdClass */
    protected $originalConfig;
    /** @var array [description] */
    protected $colors = [];

    public function reset(): self
    {
        $this->setColors([])
        ;

        return $this;
    }

    public function import(\stdClass $json): self
    {
        $this->setOriginalConfig($json)
            // ->setColors($json->colors ?? [])
        ;

        if ($json->colors) {
            $this->setColors(json_decode(json_encode($json->colors), true));
        }

        return $this;
    }

    public function export(): string
    {
        $json = $this->getOriginalConfig();

        $json->colors = $this->getColors();

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
