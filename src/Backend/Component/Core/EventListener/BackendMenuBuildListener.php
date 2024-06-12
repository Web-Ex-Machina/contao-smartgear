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

namespace WEM\SmartgearBundle\Backend\Component\Core\EventListener;

use Contao\CoreBundle\Event\MenuEvent;
use Exception;
use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as CoreConfigurationManager;
use WEM\SmartgearBundle\Config\Component\Core\Core as CoreConfig;
use WEM\SmartgearBundle\Exceptions\File\NotFound as FileNotFoundException;

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
        try {
            /** @var CoreConfig */
            $coreConfig = $this->coreConfigurationManager->load();
            if (!$coreConfig->getSgInstallComplete()) {
                $this->removeDashboardNode($event);
            }
        } catch (FileNotFoundException) {
            $this->removeDashboardNode($event);
        } catch (Exception) {
        }
    }

    protected function removeDashboardNode(MenuEvent $event): void
    {
        $tree = $event->getTree();

        if ('mainMenu' !== $tree->getName()) {
            return;
        }

        $contentNode = $tree->getChild('wem_smartgear');
        if ($contentNode) {
            $contentNode->removeChild('wem_sg_dashboard');

            if (0 === $contentNode->count()) {
                $tree->removeChild('wem_smartgear');
            }
        }
    }
}
