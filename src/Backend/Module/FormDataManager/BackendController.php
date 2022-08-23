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

namespace WEM\SmartgearBundle\Backend\Module\FormDataManager;

use Contao\CoreBundle\Controller\BackendController as ControllerBackendController;
use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as ConfigurationManager;

class BackendController extends ControllerBackendController
{
    protected $module;
    protected $type;
    protected $translator;
    protected $configurationManager;

    public function __construct(
        string $module,
        string $type,
        TranslatorInterface $translator,
        ConfigurationManager $configurationManager
    ) {
        $this->module = $module;
        $this->type = $type;
        $this->translator = $translator;
        $this->configurationManager = $configurationManager;
    }

    public function exportAll(): void
    {
        exit('coucou');
    }

    public function exportAllFromForm(): void
    {
        exit('coucou from form');
    }
}
