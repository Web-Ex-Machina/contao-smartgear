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

namespace WEM\SmartgearBundle\Backend\Component\Core\EventListener;

use Contao\ModuleModel;
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
                case 'tl_content':
                    if (!$this->security->isGranted('contao_user.smartgear_permissions', SmartgearPermissions::CORE_EXPERT)
                    ) {
                        // do not display grid_gap settings
                        $this->dcaManipulator->removeFields(['grid_gap']);
                    }
                    $this->dcaManipulator->addFieldSaveCallback('headline', [\WEM\SmartgearBundle\DataContainer\Content::class, 'cleanHeadline']);
                    $this->dcaManipulator->addFieldSaveCallback('text', [\WEM\SmartgearBundle\DataContainer\Content::class, 'cleanText']);
                break;
                case 'tl_module':
                    $nbChangeLanguageModules = ModuleModel::countByType('changelanguage');
                    if (0 === (int) $nbChangeLanguageModules) {
                        // do not display lang_selector settings
                        $this->dcaManipulator->removeFields(['wem_sg_header_add_lang_selector', 'wem_sg_header_lang_selector_bg', 'wem_sg_header_lang_selector_module']);
                    }
                break;
                case 'tl_nc_language':
                    if ($config->getSgInstallComplete()) {
                        $this->dcaManipulator->removeFields(['attachment_templates']);
                    }
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
