<?php

declare(strict_types=1);

/**
 * SMARTGEAR for Contao Open Source CMS
 * Copyright (c) 2015-2024 Web ex Machina
 *
 * @category ContaoBundle
 * @package  Web-Ex-Machina/contao-smartgear
 * @author   Web ex Machina <contact@webexmachina.fr>
 * @link     https://github.com/Web-Ex-Machina/contao-smartgear/
 */

namespace WEM\SmartgearBundle\EventListener;

use Contao\ContentElement;
use Contao\ContentModel;
use Contao\ModuleModel;
use Contao\System;
use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Contao\Module;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as CoreConfigurationManager;
use WEM\SmartgearBundle\Classes\RenderStack;
use WEM\UtilsBundle\Classes\ScopeMatcher;

#[AsHook('getContentElement',null,-1)]
class GetContentElementListener
{
    public function __construct(protected CoreConfigurationManager $configurationManager,protected readonly ScopeMatcher $scopeMatcher)
    {
    }

    public function __invoke(ContentModel $contentModel, string $buffer, Module|ContentElement $element): string
    {
        if(!$this->scopeMatcher->isFrontend()) {exit();}

        $buffer = $this->alterForPersonalDataModule($contentModel, $buffer, $element);
        $this->addToRenderStack($contentModel, $buffer, $element);

        return $buffer;
    }

    /**
     * Add the generated HTML to the RenderStack.
     *
     * @param ContentModel $contentModel The ContentModel
     * @param string       $buffer       The generated HTML
     * @param mixed        $element      The content element object (a module, form, text ...)
     */
    protected function addToRenderStack(ContentModel $contentModel, string $buffer, Module|ContentElement $element): void
    {
        $renderStack = RenderStack::getInstance();
        $renderStack->add($contentModel, $buffer, $element);
    }

    /**
     * Alter behaviour if the PersonalData module is rendered while PDM is enabled for members.
     *
     * @param ContentModel $contentModel The ContentModel
     * @param string       $buffer       The generated HTML
     * @param mixed        $element      The content element object (a module, form, text ...)
     *
     * @return string The original HTML if conditions are not met, the updated one otherwise
     */
    protected function alterForPersonalDataModule(ContentModel $contentModel, string $buffer, $element): string
    {
        if ('module' !== $contentModel->type) {
            return $buffer;
        }

        $objModule = ModuleModel::findByPk($contentModel->module);

        if (!$objModule) {
            return $buffer;
        }

        if ('personalData' !== $objModule->type) {
            return $buffer;
        }

        try {
            /** @var CoreConfigurationManager $coreConfig */
            $coreConfig = $this->configurationManager->load();
        } catch (\Exception) {
            $coreConfig = null;
        }

        if (!$coreConfig
        || $coreConfig->getSgUsePdmForMembers() // TODO : not exist ??
        ) {
            $service = System::getContainer()->get('smartgear.listener.load_data_container');
            $service->__invoke(['tl_member']);

            $buffer = $element->generate();
        }

        return $buffer;
    }
}
