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
use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as ConfigurationManager;

class Dashboard
{
    use Traits\ActionsTrait;
    use Traits\MessagesTrait;
    /** @var string */
    protected $module = '';
    /** @var string */
    protected $type = '';
    /** @var ConfigurationManager [description] */
    protected $configurationManager;
    /** @var TranslatorInterface */
    protected $translator;
    /** @var string */
    protected $strTemplate = 'be_wem_sg_install_block_dashboard';

    /**
     * Generic array of logs.
     *
     * @var array
     */
    protected $logs = [];

    public function __construct(
        ConfigurationManager $configurationManager,
        TranslatorInterface $translator,
        string $module,
        string $type
    ) {
        $this->configurationManager = $configurationManager;
        $this->translator = $translator;
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
                throw new InvalidArgumentException($this->translator->trans('WEM.SMARTGEAR.DEFAULT.AjaxNoActionSpecified', [], 'contao_default'));
            }
            switch (Input::post('action')) {
                default:
                    throw new InvalidArgumentException($this->translator->trans('WEM.SMARTGEAR.DEFAULT.AjaxInvalidActionSpecified', [Input::post('action')], 'contao_default'));
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
}
