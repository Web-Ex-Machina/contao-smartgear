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

namespace WEM\SmartgearBundle\Classes\Backend\Traits;

trait ActionsTrait
{

    protected array $actions = [];

    public function getActions(): array
    {
        return $this->actions;
    }

    protected function formatActions(?array $unformattedActions = null): array
    {
        $unformattedActions ??= $this->actions;
        $arrActions = [];
        if (\is_array($unformattedActions) && $unformattedActions !== []) {
            foreach ($unformattedActions as $action) {
                if (!\array_key_exists('v', $action)) {
                    $action['v'] = '???';
                }

                switch ($action['v']) {
                    case 2:
                        $arrAttributes = [];
                        if ($action['attrs']) {
                            if (!$action['attrs']['class']) {
                                $action['attrs']['class'] = 'tl_submit';
                            } elseif (!str_contains((string) $action['attrs']['class'], 'tl_submit')) {
                                $action['attrs']['class'] .= ' tl_submit';
                            }

                            foreach ($action['attrs'] as $k => $v) {
                                $arrAttributes[] = sprintf('%s="%s"', $k, $v);
                            }
                        }

                        $arrActions[] = sprintf(
                            '<%s %s>%s</%s>',
                            ($action['tag']) ?: 'button',
                            ([] !== $arrAttributes) ? implode(' ', $arrAttributes) : '',
                            ($action['text']) ?: 'text missing',
                            ($action['tag']) ?: 'button'
                        );
                        break;
                    default:
                        $arrActions[] = sprintf(
                            '<button type="submit" name="action" value="%s" class="tl_submit" %s>%s</button>',
                            $action['action'],
                            $action['attributes'] ?? '',
                            $action['label']
                        );
                }
            }
        }

        return $arrActions;
    }

    /**
     * Add an action.
     */
    protected function addAction($strAction, $strLabel): void
    {
        $this->actions[] = [
            'action' => $strAction,
            'label' => $strLabel,
        ];
    }
}
