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

use Contao\System;

class Message extends \Contao\Message
{
    public static function removeLatest($strScope = TL_MODE): void
    {
        if (!self::hasMessages($strScope)) {
            return;
        }

        $session = System::getContainer()->get('session');

        if (!$session->isStarted()) {
            return;
        }

        $allMessages = $session->getFlashBag()->all();

        array_pop($allMessages);

        $session->getFlashBag()->setAll($allMessages);
    }
}
