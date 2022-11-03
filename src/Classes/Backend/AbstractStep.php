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

namespace WEM\SmartgearBundle\Classes\Backend;

use Contao\Environment;
use Contao\FrontendTemplate;
use Contao\RequestToken;

class AbstractStep
{
    use Traits\FieldsTrait;
    use Traits\MessagesTrait;
    // protected $strTemplate = '';
    protected $title = '';
    protected $type = '';
    /** @var string */
    protected $module = '';
    protected $strTemplate = 'be_wem_sg_install_block_configuration_step';

    public function __construct(
        string $module,
        string $type
    ) {
        $this->module = $module;
        $this->type = $type;
    }

    public function getFilledTemplate(): FrontendTemplate
    {
        // to render the step
        $objTemplate = new FrontendTemplate($this->strTemplate);
        $objTemplate->request = Environment::get('request');
        $objTemplate->token = RequestToken::get();
        $objTemplate->module = $this->module;
        $objTemplate->type = $this->type;
        $objTemplate->title = $this->title;
        $objTemplate->fields = $this->fields;
        // Always add messages
        $objTemplate->messages = $this->messages;
        $objTemplate->actions = [];

        // And return the template, parsed.
        return $objTemplate;
    }

    public function isStepValid(): bool
    {
        // check if the step is correct
        return false;
    }

    public function do(): void
    {
        // do what is meant to be done in this step
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getModule(): string
    {
        return $this->module;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
