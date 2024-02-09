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
use tl_style_manager_archive;
use WEM\SmartgearBundle\DataContainer\Core;

class StyleManagerArchive extends Core
{
    /** @var TranslatorInterface */
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        parent::__construct();
        $this->translator = $translator;
    }

    public function listItems(array $row, string $label, DataContainer $dc, array $labels): string
    {
        return $this->translator->trans($row['title'], [], 'contao_default').(new tl_style_manager_archive())->addIdentifierInfo($row, '');
    }
}
