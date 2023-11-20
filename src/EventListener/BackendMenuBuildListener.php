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

namespace WEM\SmartgearBundle\EventListener;

use Contao\CoreBundle\Event\MenuEvent;
use Knp\Menu\ItemInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as CoreConfigurationManager;

class BackendMenuBuildListener
{
    /** @var CoreConfigurationManager */
    protected $configurationManager;
    /** @var RouterInterface */
    protected $router;
    /** @var RequestStack */
    protected $requestStack;
    /** @var TranslatorInterface */
    protected $translator;

    public function __construct(
        CoreConfigurationManager $configurationManager,
        RouterInterface $router,
        RequestStack $requestStack,
        TranslatorInterface $translator
    ) {
        $this->configurationManager = $configurationManager;
        $this->router = $router;
        $this->requestStack = $requestStack;
        $this->translator = $translator;
    }

    public function __invoke(MenuEvent $event): void
    {
        $tree = $event->getTree();

        $tree = $this->createExtranetMenu($event, $tree);
        $tree = $this->hideEmptySubMenus($tree);
        $tree = $this->putSystemMenuAtTheEnd($tree);
    }

    /**
     * Create an "administrator" menu instead of members one.
     *
     * @param ItemInterface $tree The menu tree
     *
     * @return ItemInterface The modified menu tree
     */
    protected function createExtranetMenu(MenuEvent $event, ItemInterface $tree): ItemInterface
    {
        if ('mainMenu' !== $tree->getName()) {
            return $tree;
        }

        $factory = $event->getFactory();
        // $path = $this->router->generate('contao_backend');
        $path = '/contao';

        $menu = $factory
            ->createItem('extranet')
            ->setLabel($this->translator->trans('MOD.extranet.0', [], 'contao_default'))
            ->setUri($path.'?mtg=wem_extranet')
            ->setLinkAttribute('class', 'group-wem_extranet')
            ->setLinkAttribute('title', $this->translator->trans('MOD.extranet.0', [], 'contao_default'))
            ->setLinkAttribute('onclick', "return AjaxRequest.toggleNavigation(this, 'wem_extranet', '".$path."')")
            ->setLinkAttribute('aria-controls', 'wem_extranet')
            ->setChildrenAttribute('id', 'wem_extranet')
            ->setExtra('translation_domain', false)

            ->setCurrent('member' === $this->requestStack->getCurrentRequest()->get('do') || 'mgroup' === $this->requestStack->getCurrentRequest()->get('do'))
        ;

        $memberNode = $tree->getChild('accounts')->getChild('member');
        $memberGroupsNode = $tree->getChild('accounts')->getChild('mgroup');

        $tree->getChild('accounts')->removeChild('member');
        $tree->getChild('accounts')->removeChild('mgroup');

        $menu->addChild($memberNode);
        $menu->addChild($memberGroupsNode);
        $tree->addChild($menu);

        // $children = $tree->getChildren();
        // unset($children['system']);
        // $order = array_keys($children);
        // $order[] = 'system';

        // $tree->reorderChildren($order);

        return $tree;
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
