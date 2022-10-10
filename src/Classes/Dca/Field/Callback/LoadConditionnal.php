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

use Contao\DataContainer;
use WEM\PersonalDataManagerBundle\Dca\Field\Callback\Load as PdmCallback;

class LoadConditionnal
{
    /** @var WEM\PersonalDataManagerBundle\Dca\Field\Callback\Load */
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
        if (2 === \func_num_args()) {
            return $this->invokeBackend(...\func_get_args());
        }

        return $this->invokeFrontend(...\func_get_args());
    }

    public function invokeBackend($value, DataContainer $dc)
    {
        return !(bool) $dc->activeRecord->contains_personal_data ? $value : $this->pdmCallback->__invoke(...\func_get_args());
    }

    public function invokeFrontend($value, \Contao\FrontendUser $user, \Contao\ModulePersonalData $module)
    {
        return $this->pdmCallback->__invoke(...\func_get_args());
    }
}
