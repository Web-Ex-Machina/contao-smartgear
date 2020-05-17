<?php

/**
 * SMARTGEAR for Contao Open Source CMS
 *
 * Copyright (c) 2015-2018 Web ex Machina
 *
 * @category ContaoBundle
 * @package  Web-Ex-Machina/contao-smartgear
 * @author   Web ex Machina <contact@webexmachina.fr>
 * @link     https://github.com/Web-Ex-Machina/contao-smartgear/
 */

if (isset($bundles['ContaoNewsletterBundle'])) {
    unset($GLOBALS['TL_DCA']['tl_newsletter_channel']['config']['ctable'][array_search('tl_newsletter', $GLOBALS['TL_DCA']['tl_newsletter_channel']['config']['ctable'])]);

    $GLOBALS['TL_DCA']['tl_newsletter_channel']['list']['operations']['edit']['label'] = $GLOBALS['TL_DCA']['tl_newsletter_channel']['list']['operations']['editheader']['label'];
    $GLOBALS['TL_DCA']['tl_newsletter_channel']['list']['operations']['edit']['href'] = $GLOBALS['TL_DCA']['tl_newsletter_channel']['list']['operations']['editheader']['href'];
    $GLOBALS['TL_DCA']['tl_newsletter_channel']['list']['operations']['edit']['button_callback'] = $GLOBALS['TL_DCA']['tl_newsletter_channel']['list']['operations']['editheader']['button_callback'];
    unset($GLOBALS['TL_DCA']['tl_newsletter_channel']['list']['operations']['editheader']);
}
