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

namespace WEM\SmartgearBundle\EventListener;

use Contao\CoreBundle\Event\MenuEvent;
use Knp\Menu\ItemInterface;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as CoreConfigurationManager;

class BackendMenuBuildListener
{
    /** @var CoreConfigurationManager */
    protected $configurationManager;

    public function __construct(
        CoreConfigurationManager $configurationManager
    ) {
        $this->configurationManager = $configurationManager;
    }

    public function __invoke(MenuEvent $event): void
    {
        $factory = $event->getFactory();
        $tree = $event->getTree();

        $tree = $this->hideEmptySubMenus($tree);
        $tree = $this->putSystemMenuAtTheEnd($tree);
    }

    /**
     * Hide all empty submenus of a tree.
     *
     * @param ItemInterface $tree The menu tree
     *
     * @return ItemInterface The modified menu tree
     */
    protected function hideEmptySubMenus(ItemInterface $tree): ItemInterface
    {
        if ('mainMenu' !== $tree->getName()) {
            return $tree;
        }
        $subMenus = $tree->getChildren();
        foreach ($subMenus as $index => $subMenu) {
            if (0 === \count($subMenu->getChildren())) {
                $subMenu->setDisplay(false);
            }
            $subMenus[$index] = $subMenu;
        }
        $tree->setChildren($subMenus);

        return $tree;
    }

    /**
     * Put the "system" entry at the back.
     *
     * @param ItemInterface $tree The menu tree
     *
     * @return ItemInterface The modified menu tree
     */
    protected function putSystemMenuAtTheEnd(ItemInterface $tree): ItemInterface
    {
        if ('mainMenu' !== $tree->getName()) {
            return $tree;
        }

        $children = $tree->getChildren();
        unset($children['system']);
        $order = array_keys($children);
        $order[] = 'system';

        $tree->reorderChildren($order);

        return $tree;
    }
}
