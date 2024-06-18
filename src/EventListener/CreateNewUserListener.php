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

class CreateNewUserListener
{
    public function __construct(protected array $listeners)
    {
    }

    public function __invoke(string $userId, array $data, \Contao\Module $module): void
    {
        foreach ($this->listeners as $listener) {
            $listener->__invoke($userId, $data, $module);
        }
    }
}
