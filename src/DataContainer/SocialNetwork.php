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

namespace WEM\SmartgearBundle\DataContainer;

use Contao\Backend;
use Contao\BackendUser;
use Contao\CoreBundle\Exception\AccessDeniedException;
use Contao\DataContainer;
use Contao\Image;
use Contao\Input;
use Contao\StringUtil;
use Contao\System;
use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as CoreConfigurationManager;
use WEM\SmartgearBundle\Model\SocialLink as SocialLinkModel;

class SocialNetwork extends Backend
{
    /** @var CoreConfigurationManager */
    private $configManager;

    /** @var TranslatorInterface */
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        parent::__construct();
        $this->import(BackendUser::class, 'User');
        $this->translator = $translator;
        $this->configManager = System::getContainer()->get('smartgear.config.manager.core');
    }

    public function headerCallback(array $labels, DataContainer $dc): array
    {
        $titleKey = $this->translator->trans('tl_sm_social_network_category.name.0', [], 'contao_default');
        $labels[$titleKey] = $this->translator->trans($labels[$titleKey], [], 'contao_default');

        return $labels;
    }

    public function listItems(array $row): string
    {
        $label = $this->translator->trans($row['name'], [], 'contao_default');

        if (!empty($row['icon'])) {
            $label .= ' ['.$row['icon'].']';
        }

        return $label;
    }

    /**
     * Check permissions to edit table tl_sm_social_network.
     *
     * @throws AccessDeniedException
     */
    public function checkPermission(): void
    {
        // Check current action
        switch (Input::get('act')) {
            case 'delete':
                if (!$this->canItemBeDeleted((int) Input::get('id'))) {
                    throw new AccessDeniedException('Not enough permissions to '.Input::get('act').' social network ID '.Input::get('id').'.');
                }
            break;
        }
    }

    /**
     * Return the delete social network button.
     *
     * @param array  $row
     * @param string $href
     * @param string $label
     * @param string $title
     * @param string $icon
     * @param string $attributes
     *
     * @return string
     */
    public function deleteItem($row, $href, $label, $title, $icon, $attributes)
    {
        if (!$this->canItemBeDeleted((int) $row['id'])) {
            return Image::getHtml(preg_replace('/\.svg$/i', '_.svg', $icon)).' ';
        }

        return '<a href="'.$this->addToUrl($href.'&amp;id='.$row['id']).'" title="'.StringUtil::specialchars($title).'"'.$attributes.'>'.Image::getHtml($icon, $label).'</a> ';
    }

    /**
     * Check if the social network is being used by Smartgear.
     *
     * @param int $id social network's ID
     */
    protected function isItemUsed(int $id): bool
    {
        return SocialLinkModel::countBy('pid', $id) > 0;
    }

    protected function canItemBeDeleted(int $id): bool
    {
        return $this->User->admin || !$this->isItemUsed($id);
    }
}
