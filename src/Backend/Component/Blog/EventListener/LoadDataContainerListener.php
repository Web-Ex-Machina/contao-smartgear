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

namespace WEM\SmartgearBundle\Backend\Component\Blog\EventListener;

use Symfony\Component\Security\Core\Security;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as CoreConfigurationManager;
use WEM\SmartgearBundle\Classes\Dca\Manipulator as DCAManipulator;
use WEM\SmartgearBundle\Config\Component\Core\Core as CoreConfig;
use WEM\SmartgearBundle\Exceptions\File\NotFound as FileNotFoundException;
use WEM\SmartgearBundle\Security\SmartgearPermissions;

class LoadDataContainerListener
{
    /** @var Security */
    protected $security;
    /** @var CoreConfigurationManager */
    protected $coreConfigurationManager;
    /** @var DCAManipulator */
    protected $dcaManipulator;
    /** @var string */
    protected $do;

    public function __construct(
        Security $security,
        CoreConfigurationManager $coreConfigurationManager,
        DCAManipulator $dcaManipulator
    ) {
        $this->security = $security;
        $this->coreConfigurationManager = $coreConfigurationManager;
        $this->dcaManipulator = $dcaManipulator;
    }

    public function __invoke(string $table): void
    {
        try {
            /** @var CoreConfig */
            $config = $this->coreConfigurationManager->load();
            $this->dcaManipulator->setTable($table);
            switch ($table) {
                case 'tl_news':
                    $blogConfig = $config->getSgBlog();
                    if (!$blogConfig->getSgInstallComplete()) {
                        return;
                    }

                    // limiting singleSRC field to the blog folder
                    $this->dcaManipulator->setFieldSingleSRCPath($blogConfig->getCurrentPreset()->getSgNewsFolder());

                    if (!$this->security->isGranted('contao_user.smartgear_permissions', SmartgearPermissions::CORE_EXPERT)
                    && !$this->security->isGranted('contao_user.smartgear_permissions', SmartgearPermissions::BLOG_EXPERT)
                    ) {
                        //get rid of all unnecessary actions.
                        $this->dcaManipulator->removeListOperationsEdit();
                        //get rid of all unnecessary fields
                        $fieldsKeyToKeep = ['headline', 'title', 'alias', 'author', 'date', 'time', 'jumpTo', 'pageTitle', 'description', 'teaser', 'addImage', 'singleSRC', 'published', 'start', 'stop'];
                        $this->dcaManipulator->removeOtherFields($fieldsKeyToKeep);

                        // update fields
                        $this->dcaManipulator->setFieldAliasReadonly(true);
                        $GLOBALS['TL_LANG'][$table]['teaser_legend'] = &$GLOBALS['TL_LANG']['WEMSG']['BLOG']['FORM']['paletteTeaserLabel'];
                        $GLOBALS['TL_LANG'][$table]['teaser'][0] = &$GLOBALS['TL_LANG']['WEMSG']['BLOG']['FORM']['fieldTeaserLabel'];
                        $GLOBALS['TL_LANG'][$table]['teaser'][1] = &$GLOBALS['TL_LANG']['WEMSG']['BLOG']['FORM']['fieldTeaserHelp'];
                    }
                    $this->dcaManipulator->addFieldSaveCallback('headline', [\WEM\SmartgearBundle\DataContainer\Content::class, 'cleanHeadline']);
                    $this->dcaManipulator->addFieldSaveCallback('title', [\WEM\SmartgearBundle\DataContainer\Content::class, 'cleanHeadline']);
                    $this->dcaManipulator->addFieldSaveCallback('teaser', [\WEM\SmartgearBundle\DataContainer\Content::class, 'cleanText']);
                break;
                case 'tl_content':
                    if ('news' !== $this->do) {
                        return;
                    }
                    $blogConfig = $config->getSgBlog();
                    if (!$blogConfig->getSgInstallComplete()) {
                        return;
                    }
                    // limiting singleSRC field to the blog folder
                    $this->dcaManipulator->setFieldSingleSRCPath($blogConfig->getCurrentPreset()->getSgNewsFolder());
                break;
            }
        } catch (FileNotFoundException $e) {
            //nothing
        }
    }

    public function setDo(string $do): self
    {
        $this->do = $do;

        return $this;
    }
}
