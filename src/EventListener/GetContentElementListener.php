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

use Contao\ContentElement;
use Contao\ContentModel;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as CoreConfigurationManager;
use WEM\SmartgearBundle\Config\Component\Core\Core as CoreConfig;

class GetContentElementListener
{
    /** @var CoreConfigurationManager */
    protected $coreConfigurationManager;

    public function __construct(
        CoreConfigurationManager $coreConfigurationManager
    ) {
        $this->coreConfigurationManager = $coreConfigurationManager;
    }

    public function __invoke(ContentModel $contentModel, string $buffer, $element): string
    {
        // Modify or create new $buffer here …
        try {
            /** @var CoreConfig */
            $config = $this->coreConfigurationManager->load();

            $moduleGridBuilderId = 13; //$config->getSgModuleByType('wem_sg_gridbuilder');
            if ($moduleGridBuilderId === (int) $contentModel->module) {
                // $element->setContentElementId($contentModel->id);
                // $strClass = ContentElement::findClass($contentModel->type);
                // $objElement = new $strClass($contentModel, $contentModel->strColumn);
                // $objElement->setContentElementId($contentModel->id);
                // $buffer = $objElement->generate();
                $objModule = \Contao\ModuleModel::findById($moduleGridBuilderId);

                return (new \WEM\SmartgearBundle\Module\GridBuilder($objModule))->setContentElementId((int) $contentModel->id)->generate();
            }
        } catch (\Exception $e) {
            // do nothing
        }

        return $buffer;
    }
}
