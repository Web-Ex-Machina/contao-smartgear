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
use Contao\ContentModel;
use Contao\CoreBundle\Exception\AccessDeniedException;
use Contao\DataContainer;
use Contao\Image;
use Contao\Input;
use Exception;
use tl_content;
use tl_content_calendar;
use tl_content_news;
use WEM\SmartgearBundle\Classes\Message;
use WEM\SmartgearBundle\Classes\StringUtil;

class Content extends Backend
{
    /** @var Backend */
    private $parent;

    public function __construct()
    {
        parent::__construct();
        $this->loadDataContainer('tl_content');
        $this->parent = match (Input::get('do')) {
            'news' => new tl_content_news(),
            'calendar' => new tl_content_calendar(),
            default => new tl_content(),
        };
    }

    /**
     * @throws Exception
     */
    public function getModules()
    {
        if (!method_exists($this->parent, 'getModules')) {
            throw new Exception('Method "getModules" doesn\'t exists');
        }

        return $this->parent->getModules();
    }

    /**
     * @throws Exception
     */
    public function pagePicker(DataContainer $dc)
    {
        if (!method_exists($this->parent, 'pagePicker')) {
            throw new Exception('Method "pagePicker" doesn\'t exists');
        }

        return $this->parent->pagePicker($dc);
    }

    /**
     * Check permissions to edit table tl_content.
     *
     * @throws AccessDeniedException
     */
    public function checkPermission(): void
    {
        // parent::checkPermission();
        $this->parent->checkPermission();

        if (Input::get('act') === 'delete') {
            if (!$this->canItemBeDeleted((int) Input::get('id'))) {
                throw new AccessDeniedException('Not enough permissions to '.Input::get('act').' content ID '.Input::get('id').'.');
            }
        }
    }

    /**
     * Return the delete content button.
     */
    public function deleteItem(array $row, string $href, string $label, string $title, string $icon, string $attributes): string
    {
        if (!$this->canItemBeDeleted((int) $row['id'])) {
            return Image::getHtml(preg_replace('/\.svg$/i', '_.svg', $icon)).' ';
        }

        // return parent::deleteElement($row, $href, $label, $title, $icon, $attributes);
        if (method_exists($this->parent, 'deleteElement')) {
            return $this->parent->deleteElement($row, $href, $label, $title, $icon, $attributes);
        }

        return (new tl_content())->deleteElement($row, $href, $label, $title, $icon, $attributes);
    }

    /**
     * Show a hint if a JavaScript library needs to be included in the page layout.
     */
    public function showJsLibraryHint($dc): void
    {
        if ('tl_content' === $this->parent::class) {
            $objCte = ContentModel::findByPk($dc->id);
            if (null === $objCte) {
                return;
            }

            switch ($objCte->type) {
                case 'accordionSingle':
                case 'accordionStart':
                case 'accordionStop':
                case 'gallery':
                    Message::removeLatest();
                break;
            }
        }
    }

    /**
     * Clean the tinyMCE data, see rules below
     * Rule #1 : Replace [nbsp] tags by ' '
     * Rule #2 : Find special characters and add an [nbsp] just before.
     *
     * @param DataContainer $objDc    [description]
     */
    public function cleanHeadline(mixed $varValue, DataContainer $objDc): string
    {
        $arrValue = StringUtil::deserialize($varValue);
        if ($arrValue === $varValue) {
            return StringUtil::cleanSpaces($varValue);
        }

        $arrValue['value'] = StringUtil::cleanSpaces($arrValue['value']);

        return serialize($arrValue);
    }

    /**
     * Clean the tinyMCE data, see rules below
     * Rule #1 : Replace [nbsp] tags by ' '
     * Rule #2 : Find special characters and add an [nbsp] just before.
     *
     * @param DataContainer $objDc    [description]
     */
    public function cleanText(mixed $varValue, DataContainer $objDc)
    {
        if (!\is_string($varValue)) {
            return $varValue;
        }

        return StringUtil::cleanSpaces($varValue);
    }

    /**
     * @throws Exception
     */
    public function editModule(DataContainer $dc)
    {
        if (!method_exists($this->parent, 'editModule')) {
            throw new Exception('Method "editModule" doesn\'t exists');
        }

        return $this->parent->editModule($dc);
    }

    /**
     * Check if the content is being used by Smartgear.
     *
     * @param int $id content's ID
     */
    protected function isItemUsedBySmartgear(int $id): bool
    {
        // try {
        //     /** @var CoreConfig $config */
        //     $config = $this->configManager->load();
        //     if (\in_array($id, $config->getContaoContentsIdsForAll(), true)) {
        //         return true;
        //     }
        // } catch (\Exception $e) {
        // }

        return false;
    }

    protected function canItemBeDeleted(int $id): bool
    {
        return $this->parent->User->admin || !$this->isItemUsedBySmartgear($id);
    }
}
