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

use Contao\ContentModel;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as CoreConfigurationManager;
use WEM\SmartgearBundle\Classes\RenderStack;

class GetContentElementListener
{
    /** @var CoreConfigurationManager */
    protected $configurationManager;

    public function __construct(
        CoreConfigurationManager $configurationManager
    ) {
        $this->configurationManager = $configurationManager;
    }

    public function __invoke(ContentModel $contentModel, string $buffer, $element): string
    {
        $renderStack = RenderStack::getInstance();
        $renderStack->add($contentModel, $buffer, $element);

        return $buffer;
    }
}
