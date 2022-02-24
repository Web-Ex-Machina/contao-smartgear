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
use WEM\SmartgearBundle\Classes\Config\Manager as ConfigurationManager;
use WEM\SmartgearBundle\Classes\Util;

/**
 * Back end module "smartgear".
 *
 * @author Web ex Machina <https://www.webexmachina.fr>
 */
class Block extends Controller
{
    public const MODE_DASHBOARD = 'dashboard';
    public const MODE_INSTALL = 'install';
    public const MODE_CONFIGURE = 'configure';
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

    /**
     * Array of requierments.
     *
     * @var array
     */
    protected $require = [];

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
    /** @var ConfigurationManager */
    protected $configurationManager;
    /** @var ConfigurationStepManager */
    protected $configurationStepManager;
    /** @var Dashboard */
    protected $dashboard;
    /** @var @var string */
    protected $mode = '';

    /**
     * Construct the block object.
     */
    public function __construct(
        ConfigurationManager $configurationManager,
        ConfigurationStepManager $configurationStepManager,
        Dashboard $dashboard
    ) {
        $this->configurationManager = $configurationManager;
        $this->configurationStepManager = $configurationStepManager;
        $this->dashboard = $dashboard;

        // Load the bundles, since we will need them in every block
        $this->bundles = System::getContainer()->getParameter('kernel.bundles');

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

        // return $objTemplate->parse();
        // Check if we need other modules and if yes, check if it's okay
        $blnCanManage = true;
        if ($this->require) {
            $arrMissingModules = [];
            foreach ($this->require as $strModule) {
                $objModule = System::getContainer()->get('smartgear.backend.module.'.$strModule.'.block');

                if (!$objModule->isInstalled()) {
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
            if (!$this->isInstalled()) {
                $this->setMode(self::MODE_INSTALL);
                $this->configurationStepManager->setMode($this->configurationStepManager::MODE_INSTALL);
                $objTemplate->steps = $this->configurationStepManager->parseSteps();
                $objTemplate->content = $this->configurationStepManager->parse();
            } else {
                $objTemplate = $this->parseDependingOnMode($objTemplate);
                // $objTemplate->fields = $this->fields;
                // $arrActions = [];
                // $objTemplate->logs = $this->logs;

                // Parse the actions

                // Add actions to the block
                // $objTemplate->actions = Util::formatActions($arrActions);
            }
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
                case 'toastrDisplay':
                    return ['method' => 'toastrDisplay', 'args' => [$args[0], $args[1]]];
                break;

                case 'refreshBlock':
                    return ['method' => 'refreshBlock', 'args' => ['block-'.$this->type.'-'.$this->module]];
                break;

                case 'replaceBlockContent':
                    return ['method' => 'replaceBlockContent', 'args' => ['block-'.$this->type.'-'.$this->module, $args[0]]];
                break;

                default:
                    throw new Exception('Callback inconnu : '.$key);
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function processAjaxRequest(): void
    {
        try {
            if (empty(Input::post('action'))) {
                throw new \InvalidArgumentException('No action specified');
            }
            switch (Input::post('action')) {
                case 'next':
                    $this->configurationStepManager->goToNextStep();
                    $arrResponse = ['status' => 'success', 'msg' => '', 'callbacks' => [$this->callback('refreshBlock')]];
                break;
                case 'previous':
                    $this->configurationStepManager->goToPreviousStep();
                    $arrResponse = ['status' => 'success', 'msg' => '', 'callbacks' => [$this->callback('refreshBlock')]];
                break;
                case 'setStep':
                    $this->configurationStepManager->goToStep((int) Input::post('step') ?? 0);
                    $arrResponse = ['status' => 'success', 'msg' => '', 'callbacks' => [$this->callback('refreshBlock')]];
                break;
                case 'finish':
                    $this->configurationStepManager->finish();
                    $this->setMode(self::MODE_DASHBOARD);
                    $arrResponse = ['status' => 'success', 'msg' => '', 'callbacks' => [$this->callback('refreshBlock')]];
                break;
                case 'save':
                    $this->configurationStepManager->save();
                    $arrResponse = ['status' => 'success', 'msg' => 'Les données ont été sauvegardées', 'callbacks' => [
                        $this->callback('refreshBlock'),
                        $this->callback('toastrDisplay', ['success', 'Les données ont été sauvegardées']),
                    ]];
                break;
                case 'configure':
                    $this->setMode(self::MODE_CONFIGURE);
                    $this->configurationStepManager->goToStep(0);
                    $arrResponse = ['status' => 'success', 'msg' => '', 'callbacks' => [$this->callback('refreshBlock')]];
                break;
                case 'getSteps':
                    echo $this->configurationStepManager->parseSteps();
                    exit;
                break;
                case 'parse':
                    echo $this->parse();
                    exit;
                break;
                default:
                    throw new \InvalidArgumentException(sprintf('Action "%s" is not a valid action', Input::post('action')));
                break;
            }
        } catch (Exception $e) {
            $arrResponse = ['status' => 'error', 'msg' => $e->getMessage(), 'trace' => $e->getTrace()];
        }

        // Add Request Token to JSON answer and return
        $arrResponse['rt'] = \RequestToken::get();
        echo json_encode($arrResponse);
        exit;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getLogs(): array
    {
        return $this->logs;
    }

    public function isInstalled(): bool
    {
        $config = $this->configurationManager->load();

        return (bool) $config->getSgInstallComplete();
    }

    public function getMode(): string
    {
        $mode = $this->objSession->get($this->getModeSessionKey()) ?? self::MODE_DASHBOARD;
        $this->setMode($mode);

        return $mode;
    }

    protected function parseDependingOnMode(FrontendTemplate $objTemplate): FrontendTemplate
    {
        if (self::MODE_CONFIGURE === $this->getMode()) {
            $this->configurationStepManager->setMode($this->configurationStepManager::MODE_CONFIGURE);
            $objTemplate->steps = $this->configurationStepManager->parseSteps();
            $objTemplate->content = $this->configurationStepManager->parse();
        } elseif (self::MODE_DASHBOARD === $this->getMode()) {
            $objTemplate->content = $this->dashboard->parse();
            // $objTemplate->fields = $this->dashboard->fields;
            $objTemplate->logs = $this->dashboard->getLogs();
            $objTemplate->messages = $this->dashboard->getMessages();
            $objTemplate->actions = Util::formatActions($this->dashboard->getActions());
        }

        return $objTemplate;
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

    protected function getModeSessionKey(): string
    {
        return 'sg_'.$this->module.'_mode';
    }

    protected function setMode(string $mode): self
    {
        $this->mode = $mode;

        $this->objSession->set($this->getModeSessionKey(), $mode);

        return $this;
    }
}
