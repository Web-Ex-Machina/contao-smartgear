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

namespace WEM\SmartgearBundle\EventListener;

use WEM\SmartgearBundle\Classes\CustomLanguageFileLoader;

class LoadLanguageFileListener
{
    /** @var CustomLanguageFileLoader */
    protected $customLanguageFileLoader;

    public function __construct(
        CustomLanguageFileLoader $customLanguageFileLoader
    ) {
        $this->customLanguageFileLoader = $customLanguageFileLoader;
    }

    public function __invoke(string $name, string $currentLanguage, string $cacheKey): void
    {
        $this->customLanguageFileLoader->loadCustomLanguageFile($currentLanguage);
    }
}
