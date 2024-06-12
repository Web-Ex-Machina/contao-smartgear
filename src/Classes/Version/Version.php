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

namespace WEM\SmartgearBundle\Classes\Version;

use WEM\SmartgearBundle\Exceptions\Version\BadFormatException;

class Version implements \Stringable
{
    /** @var int */
    protected $major;
    /** @var int */
    protected $minor;
    /** @var int */
    protected $fix;

    public function __toString(): string
    {
        return sprintf('%s.%s.%s', (string) $this->getMajor(), (string) $this->getMinor(), (string) $this->getFix());
    }

    public function fromString(string $version, ?string $separator = '.'): self
    {
        $fragments = explode($separator, $version);
        if (3 !== \count($fragments)) {
            throw new BadFormatException(sprintf('Version "%s" has an incorrect format', $version));
        }
        $this->major = (int) $fragments[0];
        $this->minor = (int) $fragments[1];
        $this->fix = (int) $fragments[2];

        return $this;
    }

    public function getMajor(): int
    {
        return $this->major;
    }

    public function getMinor(): int
    {
        return $this->minor;
    }

    public function getFix(): int
    {
        return $this->fix;
    }
}
