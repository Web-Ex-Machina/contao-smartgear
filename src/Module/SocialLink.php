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

namespace WEM\SmartgearBundle\Module;

use Contao\BackendModule;
use Contao\Input;
use WEM\SmartgearBundle\Model\SocialLink as SocialLinkModel;
use WEM\SmartgearBundle\Model\SocialNetwork as SocialNetworkModel;

class SocialLink extends BackendModule
{
    /**
     * Template.
     *
     * @var string
     */
    protected $strTemplate = 'mod_wem_sg_social_link';
    protected $strId = 'social_link';

    public function generate(): string
    {
        return parent::generate();
    }

    public function compile(): void
    {
        if (null !== Input::post('FORM_SUBMIT')) {
            $this->save();
        }

        $this->Template->formData = $this->prepareFormData();
        $this->Template->strId = $this->strId;
        $this->Template->links = SocialLinkModel::findAll();
        $this->Template->networks = SocialNetworkModel::findAll(['order' => 'category ASC, name ASC']);
        $this->Template->token = REQUEST_TOKEN;
    }

    protected function save(): void
    {
        $formData = $this->prepareFormData();
        $arrIdsToKeep = [];
        foreach ($formData as $index => $row) {
            // $objLink = SocialLinkModel::findByPk($row['id']) ?? new SocialLinkModel();
            $objLink = SocialLinkModel::findByPk($index + 1) ?? new SocialLinkModel();
            $objLink->id = $index + 1;
            $objLink->network = $row['network'];
            $objLink->value = $row['value'];
            $objLink->icon = $row['icon'];
            $objLink->sorting = 128 + $index;
            $objLink->tstamp = time();
            $objLink->createdAt = $objLink->createdAt ?? time();
            $objLink->save();
            $arrIdsToKeep[] = $objLink->id;
        }
        $objLinksToDelete = SocialLinkModel::findItems([
            'where' => [
                sprintf('id NOT IN (%s)', implode(',', $arrIdsToKeep)),
            ],
        ]);
        if ($objLinksToDelete) {
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
                $formData[$index] = [
                    // 'id' => $row['id'],
                    'network' => $row['network'],
                    'value' => $row['value'],
                    'icon' => $row['icon'],
                ];
            }
        } else {
            $objLinks = SocialLinkModel::findAll(['order' => 'sorting ASC']);
            $index = 0;
            foreach ($objLinks as $objLink) {
                $formData[$index] = [
                    // 'id' => $objLink->id,
                    'network' => $objLink->network,
                    'value' => $objLink->value,
                    'icon' => $objLink->icon,
                ];
                ++$index;
            }
        }

        return $formData;
    }
}
