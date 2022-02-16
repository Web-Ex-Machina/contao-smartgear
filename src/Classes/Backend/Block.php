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

use Contao\Controller;
use Contao\Environment;
use Contao\FrontendTemplate;
use Contao\Input;
use Contao\RequestToken;
use Contao\System;
use Exception;
use WEM\SmartgearBundle\Classes\Util;

/**
 * Back end module "smartgear".
 *
 * @author Web ex Machina <https://www.webexmachina.fr>
 */
class Block extends Controller
{
    /**
     * Generic array of messages.
     *
     * @var array
     */
    protected $messages = [];

    /**
     * Generic array of actions.
     *
     * @var array
     */
    protected $actions = [];

    /**
     * Generic array of logs.
     *
     * @var array
     */
    protected $logs = [];

    /** @var string */
    protected $strTemplate = 'be_wem_sg_install_block_default';

    /** @var string */
    protected $type = '';
    /** @var string */
    protected $module = '';
    /** @var string */
    protected $title = '';
    /** @var string */
    protected $icon = '';
    /** @var string */
    protected $class = '';
    /** @var ConfigurationStepManager */
    protected $configurationStepManager;

    /**
     * Construct the block object.
     */
    public function __construct(
        ConfigurationStepManager $configurationStepManager
    ) {
        $this->configurationStepManager = $configurationStepManager;

        // Load the bundles, since we will need them in every block
        $this->bundles = System::getContainer()->getParameter('kernel.bundles');

        // Load the Smartgear config, we will need it
        $this->sgConfig = Util::loadSmartgearConfig();

        // Import backend user
        $this->import('BackendUser', 'User');

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
        // > Check config
        // -> Dependencies not satisied
        // --> Display a message to inform user
        // --> RETURN
        // -> Intall not finished
        // --> Ask the ConfigurationStepManager to give us the correct step
        // --> RETURN
        // -> Everything OK
        // --> Display normal informations

        // Create the block template and add some general vars
        $objTemplate = new FrontendTemplate($this->strTemplate);
        $objTemplate->request = Environment::get('request');
        $objTemplate->token = RequestToken::get();
        $objTemplate->type = $this->type;
        $objTemplate->module = $this->module;
        $objTemplate->title = $this->title;
        $objTemplate->icon = $this->icon;
        $objTemplate->class = $this->class;
        $objTemplate->steps = $this->configurationStepManager->parseSteps();

        $objTemplate->content = $this->configurationStepManager->parse();

        return $objTemplate->parse();
        // Check if we need other modules and if yes, check if it's okay
        $blnCanManage = true;
        if ($this->require) {
            $arrMissingModules = [];
            foreach ($this->require as $strModule) {
                $objModule = Util::findAndCreateObject($strModule);
                $objModule->getStatus();

                if (1 !== $objModule->status) {
                    $arrMissingModules[] = $strModule;
                }

                if (!empty($arrMissingModules)) {
                    $this->messages = [];
                    $this->messages[] = [
                        'class' => 'tl_error', 'text' => sprintf(
                            'Vous ne pouvez pas gérer ce module tant que les dépendances suivantes ne seront pas résolues : %s',
                            implode(', ', $arrMissingModules)
                        ),
                    ];
                    $blnCanManage = false;
                }
            }
        }

        // Always add messages
        $objTemplate->messages = $this->messages;

        // Add actions only if we can manage the module
        if ($blnCanManage) {
            $objTemplate->fields = $this->fields;
            $arrActions = [];
            $objTemplate->logs = $this->logs;

            // Parse the actions

            // Add actions to the block
            $objTemplate->actions = $this->formatActions();
        }

        // And return the template, parsed.
        return $objTemplate->parse();
    }

    /**
     * Get generic callbacks for requests.
     *
     * @param [String] $key  [Key of the callbacks array]
     * @param [Array]  $args [Optional array of arguments]
     *
     * @return [Array] [Callback array]
     */
    public function callback($key, $args = null)
    {
        try {
            switch ($key) {
                case 'toastr':
                    return ['status' => $args[0], 'msg' => $args[1]];
                break;

                case 'refreshBlock':
                    return ['method' => 'refreshBlock', 'args' => ['block-'.$this->type.'-'.$this->module]];
                break;

                default:
                    throw new Exception('Callback inconnu : '.$key);
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function processAjaxRequest()
    {
        try {
            switch (Input::post('action')) {
                case 'next':
                    // try {
                        $this->configurationStepManager->goToNextStep();
                        $arrResponse = ['status' => 'success', 'msg' => '', 'callbacks' => [$this->callback('refreshBlock')]];
                    // } catch (Exception $e) {
                    //     $arrResponse = ['status' => 'error', 'msg' => $e->getMessage()];
                    // }
                break;
                case 'previous':
                    // try {
                        $this->configurationStepManager->goToPreviousStep();
                        $arrResponse = ['status' => 'success', 'msg' => '', 'callbacks' => [$this->callback('refreshBlock')]];
                    // } catch (Exception $e) {
                    //     $arrResponse = ['status' => 'error', 'msg' => $e->getMessage()];
                    // }
                break;
                case 'parse':
                   return $this->parse();
                break;
            }
        } catch (Exception $e) {
            $arrResponse = ['status' => 'error', 'msg' => $e->getMessage(), 'trace' => $e->getTrace()];
        }

        return $arrResponse;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getLogs(): array
    {
        return $this->logs;
    }

    /**
     * Reset the errors array.
     */
    protected function resetMessages(): void
    {
        $this->messages = [];
    }

    /**
     * Reset the errors array.
     *
     * @param mixed|null $strScope
     */
    protected function getMessages($strScope = null)
    {
        return $this->messages;
    }

    /**
     * Return true if there is errors in this block.
     */
    protected function hasErrors()
    {
        if (!empty($this->messages)) {
            foreach ($this->messages as $m) {
                if ('tl_error' === $m['class']) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Return true if there is updates in this block.
     */
    protected function hasUpdates()
    {
        if (!empty($this->messages)) {
            foreach ($this->messages as $m) {
                if ('tl_new' === $m['class']) {
                    return true;
                }
            }
        }

        return false;
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

    /**
     * Add an action.
     */
    protected function addAction($strAction, $strLabel): void
    {
        $this->actions[] = [
            'action' => $strAction,
            'label' => $strLabel,
        ];
    }
}
