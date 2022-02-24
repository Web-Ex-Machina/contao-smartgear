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
use Contao\Input;
use Contao\RequestToken;
use Contao\System;
use Exception;
use InvalidArgumentException;
use WEM\SmartgearBundle\Classes\Config\Manager as ConfigurationManager;

class Dashboard
{
    /** @var string */
    protected $module = '';
    /** @var string */
    protected $type = '';
    /** @var ConfigurationManager [description] */
    protected $configurationManager;
    /** @var array */
    protected $actions = [];
    /** @var string */
    protected $strTemplate = 'be_wem_sg_install_block_dashboard';
    /**
     * Generic array of messages.
     *
     * @var array
     */
    protected $messages = [];

    /**
     * Generic array of logs.
     *
     * @var array
     */
    protected $logs = [];

    public function __construct(
        ConfigurationManager $configurationManager,
        string $module,
        string $type
    ) {
        $this->configurationManager = $configurationManager;
        $this->module = $module;
        $this->type = $type;
        // Init session
        $this->objSession = System::getContainer()->get('session');
    }

    /**
     * Parse and return the block as HTML.
     *
     * @return [String] [Block HTML]
     */
    public function parse()
    {
        return $this->getFilledTemplate()->parse();
    }

    public function processAjaxRequest(): void
    {
        try {
            if (empty(Input::post('action'))) {
                throw new InvalidArgumentException('No action specified');
            }
            switch (Input::post('action')) {
                default:
                    throw new InvalidArgumentException(sprintf('Action "%s" is not a valid action', Input::post('action')));
                break;
            }
        } catch (Exception $e) {
            $arrResponse = ['status' => 'error', 'msg' => $e->getMessage(), 'trace' => $e->getTrace()];
        }

        // Add Request Token to JSON answer and return
        $arrResponse['rt'] = RequestToken::get();
        echo json_encode($arrResponse);
        exit;
    }

    /**
     * @return mixed
     */
    public function getActions(): array
    {
        return $this->actions;
    }

    public function getMessages(): array
    {
        return $this->messages;
    }

    public function getLogs(): array
    {
        return $this->logs;
    }

    protected function getFilledTemplate(): FrontendTemplate
    {
        $objTemplate = new FrontendTemplate($this->strTemplate);
        $objTemplate->request = Environment::get('request');
        $objTemplate->token = RequestToken::get();
        $objTemplate->module = $this->module;
        $objTemplate->type = $this->type;
        // $objTemplate->messages = $this->messages;
        // $objTemplate->logs = $this->logs;

        return $objTemplate;
    }

    /**
     * Add an error.
     */
    protected function addError($msg): void
    {
        $this->messages[] = [
            'class' => 'tl_error',
            'text' => $msg,
        ];
    }

    /**
     * Add an error.
     */
    protected function addInfo($msg): void
    {
        $this->messages[] = [
            'class' => 'tl_info',
            'text' => $msg,
        ];
    }

    /**
     * Add an error.
     */
    protected function addConfirm($msg): void
    {
        $this->messages[] = [
            'class' => 'tl_confirm',
            'text' => $msg,
        ];
    }

    /**
     * Add an error.
     */
    protected function addNew($msg): void
    {
        $this->messages[] = [
            'class' => 'tl_new',
            'text' => $msg,
        ];
    }
}
