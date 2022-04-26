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

class LoadDataContainerListener
{
    /** @var array */
    protected $listeners;

    public function __construct(
        array $listeners
    ) {
        $this->listeners = $listeners;
    }

    public function __invoke(string $table): void
    {
        foreach ($this->listeners as $listener) {
            $listener->__invoke($table);
        }

        // here add "explanation"/"reference" to styleManager fields ?
        if (\array_key_exists('styleManager', $GLOBALS['TL_DCA'][$table]['fields'])) {
            // $GLOBALS['TL_DCA'][$table]['fields']['styleManager']['explanation'] = 'dateFormat';
            $GLOBALS['TL_DCA'][$table]['fields']['styleManager']['reference'] = [
                'header_1' => 'h1',
                'row_1' => ['left_val', 'right_val'],
                'row_2' => ['left_val', 'right_val'],
                'row_3' => ['left_val', 'right_val'],
                'header_2' => 'h2',
                'row_4' => ['left_val', 'right_val'],
            ];
        }
    }
}
