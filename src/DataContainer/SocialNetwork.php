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
use Contao\CoreBundle\DataContainer\DataContainerOperation;
use Contao\CoreBundle\Exception\AccessDeniedException;
use Contao\DataContainer;
use Contao\Input;
use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\SmartgearBundle\Model\SocialLink as SocialLinkModel;

class SocialNetwork extends Backend
{
    public function __construct(private readonly TranslatorInterface $translator)
    {
        parent::__construct();
        $this->import(BackendUser::class, 'User');
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
        if (Input::get('act') === 'delete' && !$this->canItemBeDeleted((int) Input::get('id'))) {
            throw new AccessDeniedException('Not enough permissions to '.Input::get('act').' social network ID '.Input::get('id').'.');
        }
    }

    /**
     * Return the delete social network button.
     */
    public function deleteItem(DataContainerOperation &$config): void
    {
        if (!$this->canItemBeDeleted((int) $config->getRecord()['id'])) {
            $config->disable();
        }
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
