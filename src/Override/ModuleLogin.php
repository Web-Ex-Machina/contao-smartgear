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

namespace WEM\SmartgearBundle\Override;

use Contao\PageModel;

class ModuleLogin extends \Contao\ModuleLogin
{
    public function generate()
    {
        return parent::generate();
    }

    /**
     * Generate the module.
     */
    protected function compile(): void
    {
        parent::compile();

        if (($objTarget = $this->objModel->getRelated('wem_sg_login_pwd_lost_jumpTo')) instanceof PageModel) {
            $this->Template->wem_sg_login_pwd_lost_jumpTo_label = $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['passwordLost'];
            $this->Template->wem_sg_login_pwd_lost_jumpTo = $objTarget->getAbsoluteUrl();
        }
    }
}
