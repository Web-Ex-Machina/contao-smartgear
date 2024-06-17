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

namespace WEM\SmartgearBundle\Backend\Component\Core\ConfigurationStep;

use Contao\FrontendTemplate;
use WEM\SmartgearBundle\Classes\Backend\ConfigurationStep;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as ConfigurationManager;
use WEM\SmartgearBundle\Classes\DirectoriesSynchronizer;
use WEM\SmartgearBundle\Classes\UtilFramway;

class FramwayRetrieval extends ConfigurationStep
{

    protected DirectoriesSynchronizer $framwaySynchronizer;

    protected string $strTemplate = 'be_wem_sg_install_block_configuration_step_core_framway_retrieval';

    public function __construct(
        string $module,
        string $type,
        protected ConfigurationManager $configurationManager,
        protected UtilFramway $framwayUtil
    ) {
        parent::__construct($module, $type);
        $this->title = $GLOBALS['TL_LANG']['WEMSG']['INSTALL']['FRAMWAYRETRIEVAL']['Title'];
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

    public function framwayRetrieve(): string
    {
        set_time_limit(0);

        return $this->framwayUtil->retrieve(true);
    }

    public function framwayInstall(): string
    {
        set_time_limit(0);

        return $this->framwayUtil->install(true);
    }

    public function framwayInitialize(): string
    {
        set_time_limit(0);

        return $this->framwayUtil->initialize(true);
    }

    public function framwayBuild(): string
    {
        set_time_limit(0);

        return $this->framwayUtil->build(true);
    }

    public function checkFramwayPresence(): bool
    {
        return $this->framwayUtil->checkPresence();
    }
}
