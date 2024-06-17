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

namespace WEM\SmartgearBundle\Classes;

class CacheFileManager
{

    public function __construct(protected string $path, protected int $validityInSeconds)
    {
    }

    public function cacheFileExists(): bool
    {
        return file_exists($this->path);
    }

    public function saveCacheFile(array $data): void
    {
        $data = [
            'expiration_timestamp' => time() + $this->validityInSeconds,
            'data' => $data,
        ];

        file_put_contents($this->path, json_encode($data));
    }

    public function hasValidCache(): bool
    {
        $data = $this->retrieveFromCache();

        return $data['expiration_timestamp'] > time();
    }

    public function retrieveFromCache(): array
    {
        return json_decode(file_get_contents($this->path), true);
    }
}
