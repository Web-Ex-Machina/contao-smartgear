<?php

declare(strict_types=1);

/**
 * SMARTGEAR for Contao Open Source CMS
 * Copyright (c) 2015-2023 Web ex Machina
 *
 * @category ContaoBundle
 * @package  Web-Ex-Machina/contao-smartgear
 * @author   Web ex Machina <contact@webexmachina.fr>
 * @link     https://github.com/Web-Ex-Machina/contao-smartgear/
 */

namespace WEM\SmartgearBundle\EventListener\StyleManager;

class WidgetComponentStyleSelectGetStyleManagerArchiveModelCollectionListener
{
    public function __invoke($collection, \Oveleon\ContaoComponentStyleManager\ComponentStyleSelect $widget)
    {
        // @todo : retrieve in function of SG install

        return $collection;
    }
}
