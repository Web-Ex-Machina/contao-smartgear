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

use WEM\PersonalDataManagerBundle\Dca\Field\Callback\Save as PdmCallback;

class Save
{
    /** @var WEM\PersonalDataManagerBundle\Dca\Field\Callback\Save */
    private $pdmCallback;
    /** @var string */
    private $frontendField;
    /** @var string */
    private $table;

    public function __construct(
        PdmCallback $pdmCallback,
        string $frontendField,
        string $table
    ) {
        $this->pdmCallback = $pdmCallback;
        $this->frontendField = $frontendField;
        $this->table = $table;

        $this->pdmCallback->setFrontendField($this->frontendField)->setTable($this->table);
    }

    public function __invoke()
    {
        return $this->pdmCallback->__invoke(...\func_get_args());
    }
}
