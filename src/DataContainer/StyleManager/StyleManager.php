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

namespace WEM\SmartgearBundle\DataContainer\StyleManager;

use Contao\DataContainer;
use Symfony\Contracts\Translation\TranslatorInterface;
use Oveleon\ContaoComponentStyleManager\EventListener\DataContainer\StyleManagerListener;
use WEM\SmartgearBundle\DataContainer\Core;

class StyleManager extends Core
{
    public function __construct(private readonly TranslatorInterface $translator, private readonly StyleManagerListener $styleManagerListener)
    {
        parent::__construct();
    }

    public function headerCallback(array $labels, DataContainer $dc): array
    {
        $titleKey = $this->translator->trans('tl_style_manager_archive.title.0', [], 'contao_default');
        $labels[$titleKey] = $this->translator->trans($labels[$titleKey], [], 'contao_default');

        return $labels;
    }

    public function listItems(array $row): string
    {
        $row['title'] = $this->translator->trans($row['title'], [], 'contao_default');

        return $this->styleManagerListener->listGroupRecords($row);
    }
}
