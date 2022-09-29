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

namespace WEM\SmartgearBundle\Backend\Component\Events\ResetStep;

use Contao\Input;
use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\SmartgearBundle\Backend\Component\Events\Resetter;
use WEM\SmartgearBundle\Classes\Backend\AbstractStep;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as ConfigurationManager;
use WEM\SmartgearBundle\Config\Component\Events\Events as EventsConfig;

class General extends AbstractStep
{
    /** @var TranslatorInterface */
    protected $translator;
    /** @var ConfigurationManager */
    protected $configurationManager;
    /** @var Resetter */
    protected $resetter;

    protected $strTemplate = 'be_wem_sg_install_block_reset_step_events_general';

    public function __construct(
        string $module,
        string $type,
        TranslatorInterface $translator,
        ConfigurationManager $configurationManager,
        Resetter $resetter
    ) {
        parent::__construct($module, $type);
        $this->translator = $translator;
        $this->configurationManager = $configurationManager;
        $this->resetter = $resetter;

        $this->title = $this->translator->trans('WEMSG.EVENTS.RESET.title', [], 'contao_default');

        $resetOptions = [
            [
                'value' => EventsConfig::ARCHIVE_MODE_ARCHIVE,
                'label' => $this->translator->trans('WEMSG.EVENTS.RESET.deleteModeArchiveLabel', [], 'contao_default'),
            ],
            [
                'value' => EventsConfig::ARCHIVE_MODE_KEEP,
                'label' => $this->translator->trans('WEMSG.EVENTS.RESET.deleteModeKeepLabel', [], 'contao_default'),
            ],
            [
                'value' => EventsConfig::ARCHIVE_MODE_DELETE,
                'label' => $this->translator->trans('WEMSG.EVENTS.RESET.deleteModeDeleteLabel', [], 'contao_default'),
            ],
        ];

        $this->addSelectField('deleteMode', $this->translator->trans('WEMSG.EVENTS.RESET.deleteModeLabel', [], 'contao_default'), $resetOptions, EventsConfig::ARCHIVE_MODE_ARCHIVE, true);
    }

    public function isStepValid(): bool
    {
        // check if the step is correct
        if (!\in_array(Input::post('deleteMode'), EventsConfig::ARCHIVE_MODES_ALLOWED, true)) {
            throw new \InvalidArgumentException($this->translator->trans('WEMSG.EVENTS.RESET.deleteModeUnknown', [], 'contao_default'));
        }

        return true;
    }

    public function do(): void
    {
        // do what is meant to be done in this step
        $this->resetter->reset(Input::post('deleteMode'));
        $this->addMessages($this->resetter->getMessages());
    }
}
