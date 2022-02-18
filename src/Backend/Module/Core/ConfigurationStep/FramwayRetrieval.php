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

namespace WEM\SmartgearBundle\Backend\Module\Core\ConfigurationStep;

use Contao\FrontendTemplate;
use WEM\SmartgearBundle\Classes\Backend\ConfigurationStep;
use WEM\SmartgearBundle\Classes\Config\Manager as ConfigurationManager;
use WEM\SmartgearBundle\Classes\Util;
use WEM\SmartgearBundle\Config\Core as CoreConfig;

class FramwayRetrieval extends ConfigurationStep
{
    /** @var ConfigurationManager */
    protected $configurationManager;
    protected $strTemplate = 'be_wem_sg_install_block_configuration_step_core_framway_retrieval';

    public function __construct(
        string $module,
        string $type,
        ConfigurationManager $configurationManager
    ) {
        parent::__construct($module, $type);
        $this->title = 'Framway | Récupération';
        $this->configurationManager = $configurationManager;
    }

    public function getFilledTemplate(): FrontendTemplate
    {
        // to render the step
        $objTemplate = parent::getFilledTemplate();
        $objTemplate->framway_is_present = $this->checkFramwayPresence();
        // And return the template, parsed.
        return $objTemplate;
    }

    public function isStepValid(): bool
    {
        // check if the step is correct

        return $this->checkFramwayPresence();
    }

    public function do(): void
    {
        // do what is meant to be done in this step
    }

    public function framwayRetrieve()
    {
        set_time_limit(0);
        $config = $this->configurationManager->load();

        return Util::executeCmdLive('sh ../vendor/webexmachina/contao-smartgear/src/Resources/public/scripts/smartgear/module/core/framway_retrieve.sh ./'.$config->getSgFramwayPath());
    }

    public function framwayInstall()
    {
        set_time_limit(0);
        $config = $this->configurationManager->load();

        return Util::executeCmdLive('sh ../vendor/webexmachina/contao-smartgear/src/Resources/public/scripts/smartgear/module/core/framway_install.sh ./'.$config->getSgFramwayPath());
    }

    public function framwayInitialize()
    {
        set_time_limit(0);
        $config = $this->configurationManager->load();

        return Util::executeCmdLive('sh ../vendor/webexmachina/contao-smartgear/src/Resources/public/scripts/smartgear/module/core/framway_initialize.sh ./'.$config->getSgFramwayPath());
    }

    public function framwayBuild()
    {
        set_time_limit(0);
        $config = $this->configurationManager->load();

        return Util::executeCmdLive('sh ../vendor/webexmachina/contao-smartgear/src/Resources/public/scripts/smartgear/module/core/framway_build.sh ./'.$config->getSgFramwayPath());
    }

    public function checkFramwayPresence()
    {
        /** @var CoreConfig */
        $config = $this->configurationManager->load();

        return file_exists($config->getSgFramwayPath().\DIRECTORY_SEPARATOR.'framway.config.js') && file_exists($config->getSgFramwayPath().\DIRECTORY_SEPARATOR.'build');
    }
}
