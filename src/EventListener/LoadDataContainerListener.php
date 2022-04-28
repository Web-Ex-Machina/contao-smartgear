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

use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\SmartgearBundle\Config\Framway as FramwayConfiguration;
use WEM\SmartgearBundle\Config\FramwayTheme as FramwayThemeConfiguration;
use WEM\SmartgearBundle\Config\Manager\Framway as ConfigurationManager;
use WEM\SmartgearBundle\Config\Manager\FramwayTheme as ConfigurationThemeManager;
use WEM\SmartgearBundle\Exceptions\File\NotFound as FileNotFoundException;

class LoadDataContainerListener
{
    /** @var TranslatorInterface */
    protected $translator;
    /** @var ConfigurationManager */
    protected $framwayConfigurationManager;
    /** @var ConfigurationThemeManager */
    protected $framwayThemeConfigurationManager;
    /** @var array */
    protected $listeners;

    public function __construct(
        TranslatorInterface $translator,
        ConfigurationManager $framwayConfigurationManager,
        ConfigurationThemeManager $framwayThemeConfigurationManager,
        array $listeners
    ) {
        $this->translator = $translator;
        $this->framwayConfigurationManager = $framwayConfigurationManager;
        $this->framwayThemeConfigurationManager = $framwayThemeConfigurationManager;
        $this->listeners = $listeners;
    }

    public function __invoke(string $table): void
    {
        foreach ($this->listeners as $listener) {
            $listener->__invoke($table);
        }

        // here add "explanation"/"reference" to styleManager fields ?
        if (\array_key_exists($table, $GLOBALS['TL_DCA'])
        && \array_key_exists('fields', $GLOBALS['TL_DCA'][$table])
        && \array_key_exists('styleManager', $GLOBALS['TL_DCA'][$table]['fields'])
        ) {
            try {
                /** @var FramwayConfiguration */
                $config = $this->framwayConfigurationManager->load();
                /** @var FramwayThemeConfiguration */
                $themeConfig = $this->framwayThemeConfigurationManager->load();
                $help = [];
                $help['meaningfulLabel'] = ['headspan', $this->translator->trans('WEMSG.FRAMWAY.COLORS.meaningfulLabel', [], 'contao_default')];
                $meaningfulColors = ['primary', 'secondary', 'success', 'info', 'warning', 'error'];
                foreach ($meaningfulColors as $name) {
                    $help[$name] = [
                        '<div style="width:15px;height:15px;border:1px dotted black;" class="bg-'.$name.'"></div>',
                        $this->translator->trans(sprintf('WEMSG.FRAMWAY.COLORS.%s', $name), [], 'contao_default'),
                    ];
                }

                $help['rawLabel'] = ['headspan', $this->translator->trans('WEMSG.FRAMWAY.COLORS.rawLabel', [], 'contao_default')];
                $colors = $themeConfig->getColors();
                foreach ($colors as $name => $hexa) {
                    $help[$name] = [
                        '<div style="width:15px;height:15px;border:1px dotted black;" class="bg-'.$name.'"></div>',
                        $this->translator->trans(sprintf('WEMSG.FRAMWAY.COLORS.%s', $name), [], 'contao_default'),
                    ];
                }

                $GLOBALS['TL_DCA'][$table]['fields']['styleManager']['reference'] = $help;
            } catch (FileNotFoundException $e) {
                //nothing
            }
        }
    }
}
