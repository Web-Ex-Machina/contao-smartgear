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

namespace WEM\SmartgearBundle\Backend\Component\Faq\ResetStep;

use Contao\Input;
use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\SmartgearBundle\Backend\Component\Faq\Resetter;
use WEM\SmartgearBundle\Classes\Backend\AbstractStep;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as ConfigurationManager;
use WEM\SmartgearBundle\Config\Component\Faq\Faq as FaqConfig;

class General extends AbstractStep
{

    protected string $strTemplate = 'be_wem_sg_install_block_reset_step_faq_general';

    public function __construct(
        string                         $module,
        string                         $type,
        protected TranslatorInterface  $translator,
        protected ConfigurationManager $configurationManager,
        protected Resetter             $resetter
    ) {
        parent::__construct($module, $type);

        $this->title = $this->translator->trans('WEMSG.FAQ.RESET.title', [], 'contao_default');

        $resetOptions = [
            [
                'value' => FaqConfig::ARCHIVE_MODE_ARCHIVE,
                'label' => $this->translator->trans('WEMSG.FAQ.RESET.deleteModeArchiveLabel', [], 'contao_default'),
            ],
            [
                'value' => FaqConfig::ARCHIVE_MODE_KEEP,
                'label' => $this->translator->trans('WEMSG.FAQ.RESET.deleteModeKeepLabel', [], 'contao_default'),
            ],
            [
                'value' => FaqConfig::ARCHIVE_MODE_DELETE,
                'label' => $this->translator->trans('WEMSG.FAQ.RESET.deleteModeDeleteLabel', [], 'contao_default'),
            ],
        ];

        $this->addSelectField('deleteMode', $this->translator->trans('WEMSG.FAQ.RESET.deleteModeLabel', [], 'contao_default'), $resetOptions, FaqConfig::ARCHIVE_MODE_ARCHIVE, true);
    }

    public function isStepValid(): bool
    {
        // check if the step is correct
        if (!\in_array(Input::post('deleteMode'), FaqConfig::ARCHIVE_MODES_ALLOWED, true)) {
            throw new \InvalidArgumentException($this->translator->trans('WEMSG.FAQ.RESET.deleteModeUnknown', [], 'contao_default'));
        }

        return true;
    }

    public function do(): void
    {
        // do what is meant to be done in this step
        $this->reset(Input::post('deleteMode'));
    }

    protected function reset(string $mode): void
    {
        $this->resetter->reset($mode);
        $this->addMessages($this->resetter->getMessages(), $this->module);
    }
}
