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

namespace WEM\SmartgearBundle\Backend\Component\Faq\EventListener;

use Symfony\Component\Security\Core\Security;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as CoreConfigurationManager;
use WEM\SmartgearBundle\Classes\Dca\Manipulator as DCAManipulator;
use WEM\SmartgearBundle\Config\Component\Core\Core as CoreConfig;
use WEM\SmartgearBundle\Exceptions\File\NotFound as FileNotFoundException;

class LoadDataContainerListener
{
    /** @var Security */
    protected $security;
    /** @var CoreConfigurationManager */
    protected $coreConfigurationManager;
    /** @var DCAManipulator */
    protected $dcaManipulator;
    /** @var string */
    protected $do;

    public function __construct(
        Security $security,
        CoreConfigurationManager $coreConfigurationManager,
        DCAManipulator $dcaManipulator
    ) {
        $this->security = $security;
        $this->coreConfigurationManager = $coreConfigurationManager;
        $this->dcaManipulator = $dcaManipulator;
    }

    public function __invoke(string $table): void
    {
        try {
            /* @var CoreConfig */
            // $config = $this->coreConfigurationManager->load();
            $this->dcaManipulator->setTable($table);
            switch ($table) {
                case 'tl_faq':
                    // $faqConfig = $config->getSgFaq();
                    // if (!$faqConfig->getSgInstallComplete()) {
                    //     return;
                    // }
                    // // limiting singleSRC fierld to the blog folder
                    // $this->dcaManipulator->setFieldSingleSRCPath($faqConfig->getSgFaqFolder());
                    $this->dcaManipulator->addFieldSaveCallback('question', [\WEM\SmartgearBundle\DataContainer\Content::class, 'cleanHeadline']);
                    $this->dcaManipulator->addFieldSaveCallback('description', [\WEM\SmartgearBundle\DataContainer\Content::class, 'cleanText']);
                break;
            }
        } catch (FileNotFoundException $e) {
            //nothing
        }
    }

    public function setDo(string $do): self
    {
        $this->do = $do;

        return $this;
    }
}
