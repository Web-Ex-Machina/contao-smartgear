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

namespace WEM\SmartgearBundle\EventListener\StyleManager;

use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Oveleon\ContaoComponentStyleManager\Model\StyleManagerModel;
use Oveleon\ContaoComponentStyleManager\Widget\ComponentStyleSelect;
use Oveleon\ContaoComponentStyleManager\Model\StyleManagerArchiveModel;
use WEM\SmartgearBundle\Classes\Utils\Configuration\ConfigurationUtil;
use WEM\SmartgearBundle\Model\Configuration\Configuration;
use WEM\UtilsBundle\Classes\ScopeMatcher;

#[AsHook('styleManagerWidgetComponentStyleSelectGetStyleManagerModelCollection',"onReplaceInsertTags",-1)]
class WidgetComponentStyleSelectGetStyleManagerModelCollectionListener
{
    public function __construct(protected readonly ScopeMatcher $scopeMatcher)
    {
    }

    public function __invoke($collection, ComponentStyleSelect $widget)
    {
        if(!$this->scopeMatcher->isBackend()) {exit();}

        /** @todo : retrieve in function of SG install */
        $strTable = $widget->dataContainer->table;
        $strId = $widget->activeRecord->id;

        // now we have the element
        // tl_content => tl_article (or whatever ptable) => tl_page => root_page => SG install
        // tl_formfield => tl_form => either directly in SG install, or referenced in a content in an article in a page for SG install
        $objConfiguration = ConfigurationUtil::findConfigurationForItem($strTable, (int) $strId);

        // get the archives related to $objConfiguration->id
        if ($objConfiguration instanceof Configuration) {
            $collection = StyleManagerModel::findByTable($strTable, [
                'order' => 'sorting',
                'column' => 'pid IN (SELECT sma.id FROM '.StyleManagerArchiveModel::getTable().' sma WHERE sma.wem_sg_install = '.$objConfiguration->id.')',
            ]);
        }

        return $collection;
    }
}
