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

namespace WEM\SmartgearBundle\Backend;

use Contao\BackendModule;
use Contao\DataContainer;
use Contao\Input;
use Contao\Model\Collection;
use Contao\System;
use WEM\SmartgearBundle\Model\SocialLink as SocialLinkModel;
use WEM\SmartgearBundle\Model\SocialNetwork as SocialNetworkModel;
use WEM\SmartgearBundle\Security\SmartgearPermissions;

class SocialLink extends BackendModule
{
    /**
     * Template.
     *
     * @var string
     */
    protected $strTemplate = 'be_wem_sg_social_link';

    protected $security;

    protected string $strId = 'social_link';
    protected mixed $contaoCsrfTokenManager;

    public function __construct(DataContainer|null $dc = null)
    {
        parent::__construct($dc);
        $this->security = System::getContainer()->get('security.helper');
        $this->contaoCsrfTokenManager = System::getContainer()->getParameter('@contao.csrf.token_manager');
    }

    public function generate(): string
    {
        return parent::generate();
    }

    protected function compile(): void
    {
        if (null !== Input::post('FORM_SUBMIT')) {
            $this->save();
        }

        $this->Template->formData = $this->prepareFormData();
        $this->Template->strId = $this->strId;
        $this->Template->links = SocialLinkModel::findAll();
        $this->Template->networks = SocialNetworkModel::findAll(['order' => 'pid ASC, name ASC']);
        $this->Template->modeExpert = $this->security->isGranted(SmartgearPermissions::SOCIALLINK_EXPERT);
        $this->Template->token = $this->contaoCsrfTokenManager->getDefaultTokenValue();
    }

    protected function save(): void
    {
        $formData = $this->prepareFormData();
        $arrIdsToKeep = [];
        foreach ($formData as $index => $row) {
            $objLink = SocialLinkModel::findByPk($index + 1) ?? new SocialLinkModel();
            $objLink->id = $index + 1;
            $objLink->pid = $row['pid'];
            $objLink->value = $row['value'];
            $objLink->icon = $row['icon'];
            $objLink->sorting = 128 + $index;
            $objLink->tstamp = time();
            $objLink->createdAt ??= time();
            $objLink->save();
            $arrIdsToKeep[] = $objLink->id;
        }

        $this->removeUnusedLinks($arrIdsToKeep);
    }

    protected function removeUnusedLinks(array $arrIdsToKeep): void
    {
        $arrConfig = $arrIdsToKeep === []
                    ? []
                    : [
                        'where' => [
                            sprintf('id NOT IN (%s)', implode(',', $arrIdsToKeep)),
                        ],
                    ];
        $objLinksToDelete = SocialLinkModel::findItems($arrConfig);
        if ($objLinksToDelete instanceof Collection) {
            while ($objLinksToDelete->next()) {
                $objLinksToDelete->delete();
            }
        }
    }

    protected function prepareFormData(): array
    {
        $formData = [];
        if (null !== Input::post('FORM_SUBMIT')) {
            $rows = Input::post($this->strId);
            foreach ($rows as $index => $row) {
                if (!empty($row['value'])) {
                    $formData[$index] = $row;
                }
            }
        } else {
            $objLinks = SocialLinkModel::findAll(['order' => 'sorting ASC']);
            $index = 0;
            foreach ($objLinks as $objLink) {
                $formData[$index] = [
                    'pid' => $objLink->pid,
                    'value' => $objLink->value,
                    'icon' => $objLink->icon,
                ];
                ++$index;
            }
        }

        return $formData;
    }
}
