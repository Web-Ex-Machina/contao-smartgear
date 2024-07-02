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

namespace WEM\SmartgearBundle\Module;

use Contao\BackendTemplate;
use Contao\Module;
use Contao\System;
use Exception;
use WEM\SmartgearBundle\Model\SocialLink as SocialLinkModel;

/**
 * Front end module.
 *
 * @author Web ex Machina <https://www.webexmachina.fr>
 */
class SocialLink extends Module
{
    /**
     * Template.
     *
     * @var string
     */
    protected $strTemplate = 'mod_wem_sg_social_link';

    /**
     * Display a wildcard in the back end.
     */
    public function generate(): string
    {
        $scopeMatcher = System::getContainer()->get('wem.scope_matcher');
        if ($scopeMatcher->isBackend()) {

            $objTemplate = new BackendTemplate('be_wildcard');

            $objTemplate->wildcard = '### '.mb_strtoupper((string) $GLOBALS['TL_LANG']['MOD']['social_link'][0], 'UTF-8').' ###';
            $objTemplate->title = $this->headline;
            $objTemplate->id = $this->id;
            $objTemplate->link = $this->name;
            $objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id='.$this->id;

            return $objTemplate->parse();
        }

        return parent::generate();
    }

    /**
     * Generate the module.
     */
    protected function compile(): void
    {
        try {
            $this->Template->links = SocialLinkModel::findAll();
        } catch (Exception $exception) {
            $this->Template->blnError = true;
            $this->Template->strError = $exception->getMessage();
        }
    }
}
