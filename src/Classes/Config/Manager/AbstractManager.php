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

namespace WEM\SmartgearBundle\Classes\Config\Manager;

use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\SmartgearBundle\Exceptions\File\NotFound as FileNotFoundException;

abstract class AbstractManager implements ManagerInterface
{
    /** @var TranslatorInterface */
    protected $translator;
    /** @var string */
    protected $configurationFilePath;

    public function __construct(
        TranslatorInterface $translator
    ) {
        $this->translator = $translator;
    }

    /**
     * Retrieve content from configuration file.
     */
    protected function retrieveConfigurationFromFile(): string
    {
        if (!file_exists($this->configurationFilePath)) {
            throw new FileNotFoundException($this->translator->trans('WEMSG.CONFIGURATIONMANAGER.fileNotFound', [], 'contao_default'));
        }

        return file_get_contents($this->configurationFilePath);
    }
}
