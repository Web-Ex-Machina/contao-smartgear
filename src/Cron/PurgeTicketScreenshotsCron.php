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

namespace WEM\SmartgearBundle\Cron;

use Contao\Folder;
use WEM\SmartgearBundle\Config\Component\Core\Core as CoreConfig;

class PurgeTicketScreenshotsCron
{
    public function __invoke(): void
    {
        $objFolder = new Folder(CoreConfig::DEFAULT_CLIENT_FILES_FOLDER.\DIRECTORY_SEPARATOR.'tickets');
        $objFolder->purge();
    }
}
