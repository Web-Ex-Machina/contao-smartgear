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

namespace WEM\SmartgearBundle\Command;

use Contao\System;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Contao\CoreBundle\Framework\ContaoFramework;

class AbstractCommand extends Command
{
    public function __construct(protected ContaoFramework $framework)
    {
        parent::__construct();

        $this->framework->initialize();

        System::loadLanguageFile('default', 'en');
    }

    protected function configure(): void
    {
        $this
            ->addOption('format', null, InputOption::VALUE_REQUIRED, 'The output format (txt, json)', 'txt')
        ;
    }

    protected function isJson(InputInterface $input): bool
    {
        $format = $input->getOption('format');

        if (!\in_array($format, ['json', 'txt'], true)) {
            throw new \InvalidArgumentException('This command only supports the "txt" and "json" formats.');
        }

        return 'json' === $format;
    }
}
