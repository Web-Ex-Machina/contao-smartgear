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

namespace WEM\SmartgearBundle\Classes\Dca\Field\Callback;

use WEM\PersonalDataManagerBundle\Dca\Field\Callback\Load as PdmCallback;

class Load
{
    /** @var WEM\PersonalDataManagerBundle\Dca\Field\Callback\Load */
    private $pdmCallback;

    public function __construct(
        PdmCallback $pdmCallback,
        private string $frontendField,
        private string $table
    ) {
        $this->pdmCallback = $pdmCallback;

        $this->pdmCallback->setFrontendField($this->frontendField)->setTable($this->table);
    }

    public function __invoke()
    {
        return $this->pdmCallback->__invoke(...\func_get_args());
    }
}
