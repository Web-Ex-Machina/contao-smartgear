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

namespace WEM\SmartgearBundle\Backend\Module\FormDataManager\EventListener;

use Contao\CoreBundle\Event\MenuEvent;
use Contao\System;
use Exception;
use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as CoreConfigurationManager;
use WEM\SmartgearBundle\Config\Component\Core\Core as CoreConfig;
use WEM\SmartgearBundle\Config\Module\FormDataManager\FormDataManager as FormDataManagerConfig;

class BackendMenuBuildListener
{
    /** @var TranslatorInterface */
    protected $translator;

    /** @var CoreConfigurationManager */
    protected $coreConfigurationManager;

    public function __construct(
        TranslatorInterface $translator,
        CoreConfigurationManager $coreConfigurationManager
    ) {
        $this->translator = $translator;
        $this->coreConfigurationManager = $coreConfigurationManager;
    }

    public function __invoke(MenuEvent $event): void
    {
        $backendRoutePrefix = System::getContainer()->getParameter('contao.backend.route_prefix');
        try {
            /** @var CoreConfig */
            $coreConfig = $this->coreConfigurationManager->load();
            /** @var FormDataManagerConfig */
            $fdmConfig = $coreConfig->getSgFormDataManager();
            if ($coreConfig->getSgInstallComplete()
            && $fdmConfig->getSgInstallComplete()
            ) {
                $factory = $event->getFactory();
                $tree = $event->getTree();

                if ('mainMenu' !== $tree->getName()) {
                    return;
                }

                $contentNode = $tree->getChild('content');

                $node = $factory
                    ->createItem('form-data-manager-module')
                    ->setUri($backendRoutePrefix.'?do=wem_sg_form_data_manager')
                    ->setLabel($this->translator->trans('MOD.wem_sg_form_data_manager.0', [], 'contao_default'))
                    ->setLinkAttribute('title', $this->translator->trans('MOD.wem_sg_form_data_manager.1', [], 'contao_default'))
                    ->setLinkAttribute('class', 'form-data-manager')
                    // ->setCurrent(/* … */)
                ;

                $contentNode->addChild($node);
            }
        } catch (Exception $e) {
        }
    }
}
