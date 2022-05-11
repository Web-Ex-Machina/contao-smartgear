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

namespace WEM\SmartgearBundle\Backend\Component\FormContact\EventListener;

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
            /** @var CoreConfig */
            $config = $this->coreConfigurationManager->load();
            $this->dcaManipulator->setTable($table);
            switch ($table) {
                case 'tl_form':
                    $formContactConfig = $config->getSgFormContact();
                    if (!$formContactConfig->getSgInstallComplete()) {
                        return;
                    }
                    // limiting singleSRC fierld to the blog folder
                    $this->dcaManipulator->setFieldSingleSRCPath($formContactConfig->getSgFormContactFolder());
                break;
            }
        } catch (FileNotFoundException $e) {
            //nothing
        }
    }
}
