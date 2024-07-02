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
use Contao\CoreBundle\Csrf\ContaoCsrfTokenManager;
use Contao\System;
use Exception;
use InvalidArgumentException;
use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as ConfigurationManager;

class Dashboard
{
    use Traits\ActionsTrait;
    use Traits\MessagesTrait;

    protected string $strTemplate = 'be_wem_sg_install_block_dashboard';

    protected mixed $objSession;

    protected array $logs = [];

    public function __construct(
        protected ConfigurationManager              $configurationManager,
        protected TranslatorInterface               $translator,
        protected string                            $module,
        protected readonly ContaoCsrfTokenManager   $contaoCsrfTokenManager,
        protected string                            $type
    ) {
        // Init session
        $this->objSession = System::getContainer()->get('session');
    }

    /**
     * Parse and return the block as HTML.
     *
     * @return string Block HTML
     */
    public function parse(): string
    {
        return $this->getFilledTemplate()->parse();
    }

    public function processAjaxRequest(): void
    {
        try {
            if (empty(Input::post('action'))) {
                throw new InvalidArgumentException($this->translator->trans('WEM.SMARTGEAR.DEFAULT.AjaxNoActionSpecified', [], 'contao_default'));
            }

            throw new InvalidArgumentException($this->translator->trans('WEM.SMARTGEAR.DEFAULT.AjaxInvalidActionSpecified', [Input::post('action')], 'contao_default'));
        } catch (Exception $exception) {
            $arrResponse = ['status' => 'error', 'msg' => $exception->getMessage(), 'trace' => $exception->getTrace()];
        }

        // Add Request Token to JSON answer and return
        $arrResponse['rt'] = $this->contaoCsrfTokenManager->getDefaultTokenValue();
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
        $objTemplate->token = $this->contaoCsrfTokenManager->getDefaultTokenValue();
        $objTemplate->module = $this->module;
        $objTemplate->type = $this->type;
        // $objTemplate->messages = $this->messages;
        // $objTemplate->logs = $this->logs;

        return $objTemplate;
    }
}
