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
use Contao\FrontendUser;
use Contao\ModulePersonalData;
use WEM\PersonalDataManagerBundle\Dca\Field\Callback\Load as PdmCallback;

class LoadConditionnal
{
    public function __construct(
        private readonly PdmCallback $pdmCallback,
        private readonly string $frontendField,
        private readonly string $table
    ) {
        $this->pdmCallback->setFrontendField($this->frontendField)->setTable($this->table);
    }

    /**
     * @throws \Exception
     */
    public function __invoke()
    {
        if (2 === \func_num_args()) {
            return $this->invokeBackend(...\func_get_args());
        }

        return $this->invokeFrontend(...\func_get_args());
    }

    public function invokeBackend($value, DataContainer $dc)
    {
        return $dc->activeRecord->contains_personal_data ? $this->pdmCallback->__invoke(...\func_get_args()) : $value;
    }

    /**
     * @throws \Exception
     */
    public function invokeFrontend($value, FrontendUser $user, ModulePersonalData $module)
    {
        return $this->pdmCallback->__invoke(...\func_get_args());
    }
}
