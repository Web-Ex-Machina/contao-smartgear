<?php

declare(strict_types=1);

/**
 * SMARTGEAR for Contao Open Source CMS
 * Copyright (c) 2015-2023 Web ex Machina
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
use Contao\System;
use Exception;
use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as ConfigurationManager;
use WEM\SmartgearBundle\Exceptions\File\NotFound as FileNotFoundException;

/**
 * Back end module "smartgear".
 *
 * @author Web ex Machina <https://www.webexmachina.fr>
 */
class Block extends Controller
{
    use Traits\ActionsTrait;
    use Traits\MessagesTrait;
    public const MODE_DASHBOARD = 'dashboard';
    public const MODE_INSTALL = 'install';
    public const MODE_CONFIGURE = 'configure';
    protected array $logs = [];
    protected array $require = [];
    protected string $strTemplate = 'be_wem_sg_install_block_default';
    protected string $type = '';
    protected string $module = '';
    protected string $title = '';
    protected string $icon = '';
    protected string $class = '';
    protected string $mode = '';
    protected mixed $bundles ;
    protected mixed $objSession ;
    protected mixed $contaoCsrfTokenManager;
    /**
     * Construct the block object.
     */
    public function __construct(
        protected ConfigurationManager $configurationManager,
        protected ConfigurationStepManager $configurationStepManager,
        protected Dashboard $dashboard,
        protected TranslatorInterface $translator
    ) {
        // Load the bundles, since we will need them in every block
        $this->bundles = System::getContainer()->getParameter('kernel.bundles');

        // Import backend user
        $this->import('BackendUser', 'User');

        // Init session
        $this->objSession = System::getContainer()->get('session');
        $this->contaoCsrfTokenManager = System::getContainer()->getParameter('@contao.csrf.token_manager');
        Parent::__construct();
    }

    /**
     * Parse and return the block as HTML.
     *
     * @return string Block HTML
     */
    public function parse(): string
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
        $objTemplate->token = $this->contaoCsrfTokenManager->getDefaultTokenValue();
        $objTemplate->type = $this->type;
        $objTemplate->module = $this->module;
        $objTemplate->title = $this->title;
        $objTemplate->icon = $this->icon;
        $objTemplate->class = $this->class;

        $blnCanManage = $this->checkRequirements();

        // Add actions only if we can manage the module
        if ($blnCanManage) {
            if (!$this->isInstalled()) {
                if (self::MODE_DASHBOARD === $this->getMode()) {
                    $objTemplate = $this->parseDependingOnMode($objTemplate);
                } else {
                    $this->setMode(self::MODE_INSTALL);
                    $this->configurationStepManager->setMode($this->configurationStepManager::MODE_INSTALL);
                    $objTemplate->steps = $this->configurationStepManager->parseSteps();
                    $objTemplate->content = $this->configurationStepManager->parse();
                }

                // Always add messages
                $objTemplate->messages = $this->getMessages($this->module);
            } else {
                // if module installed but mode install, nope, dashboard mode
                if (self::MODE_INSTALL === $this->getMode()) {
                    $this->setMode(self::MODE_DASHBOARD);
                }

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
     * @param string $key [Key of the callbacks array]
     * @param array|null $args [Optional array of arguments]
     *
     * @return array [Callback array]
     * @throws Exception
     */
    public function callback(string $key, array $args = null): array
    {
        return match ($key) {
            'toastrDisplay' => ['method' => 'toastrDisplay', 'args' => [$args[0], $args[1]]],
            'refreshBlock' => ['method' => 'refreshBlock', 'args' => ['block-'.$this->type.'-'.$this->module]],
            'replaceBlockContent' => ['method' => 'replaceBlockContent', 'args' => ['block-'.$this->type.'-'.$this->module, $args[0]]],
            'reload' => ['method' => 'reload', 'args' => []],
            default => throw new Exception('Callback inconnu : '.$key),
        };
    }

    public function processAjaxRequest(): void
    {
        try {
            if (empty(Input::post('action'))) {
                throw new \InvalidArgumentException($this->translator->trans('WEM.SMARTGEAR.DEFAULT.AjaxNoActionSpecified', [], 'contao_default'));
            }

            switch (Input::post('action')) {
                case 'next':
                    $this->goToNextStep();
                    $arrResponse = ['status' => 'success', 'msg' => '', 'callbacks' => [$this->callback('refreshBlock')]];
                break;
                case 'previous':
                    $this->goToPreviousStep();
                    $arrResponse = ['status' => 'success', 'msg' => '', 'callbacks' => [$this->callback('refreshBlock')]];
                break;
                case 'setStep':
                    $this->goToStep((int) Input::post('step') ?? 0);
                    $arrResponse = ['status' => 'success', 'msg' => '', 'callbacks' => [$this->callback('refreshBlock')]];
                break;
                case 'finish':
                    $arrResponse = $this->finish();
                break;
                case 'save':
                    $this->save();
                    $arrResponse = ['status' => 'success', 'msg' => $this->translator->trans('WEM.SMARTGEAR.DEFAULT.dataSaved', [], 'contao_default'), 'callbacks' => [
                        $this->callback('refreshBlock'),
                        $this->callback('toastrDisplay', ['success', $this->translator->trans('WEM.SMARTGEAR.DEFAULT.dataSaved', [], 'contao_default')]),
                    ]];
                break;
                case 'install':
                    $this->setMode(self::MODE_INSTALL);
                    $this->configurationStepManager->goToStep(0);
                    $arrResponse = ['status' => 'success', 'msg' => '', 'callbacks' => [$this->callback('refreshBlock')]];
                break;
                case 'configure':
                    $this->setMode(self::MODE_CONFIGURE);
                    $this->configurationStepManager->goToStep(0);
                    $arrResponse = ['status' => 'success', 'msg' => '', 'callbacks' => [$this->callback('refreshBlock')]];
                break;
                case 'dashboard':
                    $this->setMode(self::MODE_DASHBOARD);
                    $arrResponse = ['status' => 'success', 'msg' => '', 'callbacks' => [$this->callback('refreshBlock')]];
                break;
                case 'getSteps':
                    echo $this->parseSteps();
                    exit;
                case 'parse':
                    echo $this->parse();
                    exit;
                default:
                    throw new \InvalidArgumentException($this->translator->trans('WEM.SMARTGEAR.DEFAULT.AjaxInvalidActionSpecified', [Input::post('action')], 'contao_default'));
            }
        } catch (Exception $exception) {
            $arrResponse = ['status' => 'error', 'msg' => $exception->getMessage(), 'trace' => $exception->getTrace()];
        }

        // Add Request Token to JSON answer and return
        $arrResponse['rt'] = $this->contaoCsrfTokenManager->getDefaultTokenValue();
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
        try {
            $config = $this->configurationManager->load();
        } catch (FileNotFoundException) {
            $config = $this->configurationManager->new();
            $this->configurationManager->save($config);
        }

        return (bool) $config->getSgInstallComplete();
    }

    public function getMode(): string
    {
        $mode = $this->objSession->get($this->getModeSessionKey()) ?? self::MODE_DASHBOARD;
        $this->setMode($mode);

        return $mode;
    }

    protected function checkRequirements(): bool
    {
        $requiermentsMet = true;
        if ($this->require) {
            $arrMissingModules = [];
            foreach ($this->require as $type => $block) {
                $objModule = System::getContainer()->get('smartgear.backend.'.$type.'.'.$block.'.block');

                if (!$objModule->isInstalled()) {
                    $arrMissingModules[] = $block;
                }

                if ($arrMissingModules !== []) {
                    $this->messages = [];
                    $this->messages[] = [
                        'class' => 'tl_error', 'text' => $this->translator->trans('WEM.SMARTGEAR.DEFAULT.MissingDependencies', [implode(', ', $arrMissingModules)], 'contao_default'),
                    ];
                    $requiermentsMet = false;
                }
            }
        }

        return $requiermentsMet;
    }

    protected function goToNextStep(): void
    {
        switch ($this->getMode()) {
            case self::MODE_CONFIGURE:
            case self::MODE_INSTALL:
                $this->configurationStepManager->goToNextStep();
            break;
        }
    }

    protected function goToPreviousStep(): void
    {
        switch ($this->getMode()) {
            case self::MODE_CONFIGURE:
            case self::MODE_INSTALL:
                $this->configurationStepManager->goToPreviousStep();
            break;
        }
    }

    protected function goToStep(int $stepIndex): void
    {
        switch ($this->getMode()) {
            case self::MODE_CONFIGURE:
            case self::MODE_INSTALL:
                $this->configurationStepManager->goToStep($stepIndex);
            break;
        }
    }

    protected function finish(): array
    {
        switch ($this->getMode()) {
            case self::MODE_CONFIGURE:
            case self::MODE_INSTALL:
                $this->configurationStepManager->finish();
                $this->setMode(self::MODE_DASHBOARD);
                $arrResponse = ['status' => 'success', 'msg' => '', 'callbacks' => [$this->callback('refreshBlock')]];
            break;
        }

        return $arrResponse;
    }

    protected function save(): void
    {
        switch ($this->getMode()) {
            case self::MODE_CONFIGURE:
            case self::MODE_INSTALL:
                $this->configurationStepManager->save();
            break;
        }
    }

    protected function parseSteps(): ?string
    {
        return match ($this->getMode()) {
            self::MODE_CONFIGURE,
            self::MODE_INSTALL => $this->configurationStepManager->parseSteps(),
            default => null,
        };
    }

    protected function parseDependingOnMode(FrontendTemplate $objTemplate): FrontendTemplate
    {
        if (self::MODE_CONFIGURE === $this->getMode()) {
            $this->configurationStepManager->setMode($this->configurationStepManager::MODE_CONFIGURE);
            $objTemplate->steps = $this->configurationStepManager->parseSteps();
            $objTemplate->content = $this->configurationStepManager->parse();
            $objTemplate->messages = $this->getMessages($this->module);
        } elseif (self::MODE_DASHBOARD === $this->getMode()) {
            $objTemplate->content = $this->dashboard->parse();
            // $objTemplate->fields = $this->dashboard->fields;
            $objTemplate->logs = $this->dashboard->getLogs();
            $objTemplate->messages = $this->dashboard->getMessages($this->module);
            $objTemplate->actions = $this->formatActions($this->dashboard->getActions());
        }

        return $objTemplate;
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
