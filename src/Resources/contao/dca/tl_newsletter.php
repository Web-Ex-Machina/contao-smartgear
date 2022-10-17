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

use Contao\CoreBundle\DataContainer\PaletteManipulator;
use Contao\DataContainer;
use Contao\NewsletterModel;
use Contao\System;
use WEM\SmartgearBundle\Classes\Dca\Driver\DC_Table_Newsletter;
use WEM\SmartgearBundle\Classes\Dca\Manipulator as DCAManipulator;

/*
 * Remove PID from tl_newsletter
 * 2018-10-29 : Bad Idea (Generate issues in Backend)
 */
// unset($GLOBALS['TL_DCA']['tl_newsletter']['config']['ptable'], $GLOBALS['TL_DCA']['tl_newsletter']['config']['sql']['keys']['pid'], $GLOBALS['TL_DCA']['tl_newsletter']['fields']['pid']);

$bundles = System::getContainer()->getParameter('kernel.bundles');
if (isset($bundles['ContaoNewsletterBundle'])) {
    $GLOBALS['TL_DCA']['tl_newsletter']['config']['dataContainer'] = DC_Table_Newsletter::class;
    $GLOBALS['TL_DCA']['tl_newsletter']['config']['onsubmit_callback'][] = function (DataContainer $dc): void {
        if (!$dc->id) {
            return;
        }
        $objNewsletter = NewsletterModel::findById($dc->id);
        $channels = unserialize($objNewsletter->channels);
        if (!$channels) {
            return;
        }
        $objNewsletter->pid = $channels[0];
        $objNewsletter->save();
    };

    $GLOBALS['TL_DCA']['tl_newsletter']['list']['sorting']['mode'] = 1;
    $GLOBALS['TL_DCA']['tl_newsletter']['list']['sorting']['flag'] = 1;
    $GLOBALS['TL_DCA']['tl_newsletter']['list']['label']['fields'] = ['subject'];
    $GLOBALS['TL_DCA']['tl_newsletter']['list']['label']['format'] = '%s';
    $GLOBALS['TL_DCA']['tl_newsletter']['list']['label']['label_callback'] = $GLOBALS['TL_DCA']['tl_newsletter']['list']['sorting']['child_record_callback'];

    $GLOBALS['TL_DCA']['tl_newsletter']['list']['label']['group_callback'] = function (string $group, int $mode, $field, array $row, DataContainer $dc) {
        return $row['sent'] ? $GLOBALS['TL_LANG']['tl_newsletter']['sent'] : $GLOBALS['TL_LANG']['tl_newsletter']['notSent'];
    };

    PaletteManipulator::create()
        ->addField('channels', 'alias')
        ->applyToPalette('default', 'tl_newsletter')
    ;

    DCAManipulator::create('tl_newsletter')
        ->addField('channels', [
            'label' => &$GLOBALS['TL_LANG']['tl_newsletter']['channels'],
            'exclude' => true,
            'inputType' => 'checkbox',
            'foreignKey' => 'tl_newsletter_channel.title',
            'eval' => ['chosen' => true, 'multiple' => true, 'tl_class' => 'clr'],
            'sql' => 'blob NULL',
        ])
    ;
}
