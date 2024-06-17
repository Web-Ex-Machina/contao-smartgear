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

    protected string $do;

    public function __construct(
        protected Security                 $security,
        protected CoreConfigurationManager $coreConfigurationManager,
        protected DCAManipulator           $dcaManipulator)
    {
    }

    public function __invoke(string $table): void
    {
        try {
            /** @var CoreConfig $config */
            // $config = $this->coreConfigurationManager->load();
            $this->dcaManipulator->setTable($table);
            if ($table === 'tl_faq') {
                // $faqConfig = $config->getSgFaq();
                // if (!$faqConfig->getSgInstallComplete()) {
                //     return;
                // }
                // // limiting singleSRC fierld to the blog folder
                // $this->dcaManipulator->setFieldSingleSRCPath($faqConfig->getSgFaqFolder());
                $this->dcaManipulator->addFieldSaveCallback('question', static fn($varValue, \Contao\DataContainer $objDc) => (new \WEM\SmartgearBundle\DataContainer\Content())->cleanHeadline($varValue, $objDc));
                $this->dcaManipulator->addFieldSaveCallback('description', static fn($varValue, \Contao\DataContainer $objDc) => (new \WEM\SmartgearBundle\DataContainer\Content())->cleanText($varValue, $objDc));
            }
        } catch (FileNotFoundException) {
            //nothing
        }
    }

    public function setDo(string $do): self
    {
        $this->do = $do;

        return $this;
    }
}
