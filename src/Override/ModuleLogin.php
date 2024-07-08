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

use Contao\CoreBundle\DependencyInjection\Attribute\AsFrontendModule;
use Contao\PageModel;
use Contao\System;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[AsFrontendModule(type: 'user', name:'login')]
class ModuleLogin extends \Contao\ModuleLogin
{

    public function generate(): string
    {
        return parent::generate();
    }

    /**
     * Generate the module.
     * @throws \Exception
     */
    protected function compile(): void
    {

        parent::compile();

        /* @var UrlGeneratorInterface $routeGenerator*/
        $routeGenerator = System::getContainer()->get('contao.routing.content_url_generator');

        if (($objTarget = $this->objModel->getRelated('wem_sg_login_pwd_lost_jumpTo')) instanceof PageModel) {
            $this->Template->wem_sg_login_pwd_lost_jumpTo_label = $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['passwordLost'];
            $this->Template->wem_sg_login_pwd_lost_jumpTo = $routeGenerator->generate($objTarget->name, [], UrlGeneratorInterface::ABSOLUTE_URL);
        }

        if (($objTarget = $this->objModel->getRelated('wem_sg_login_register_jumpTo')) instanceof PageModel) {
            $this->Template->wem_sg_login_register_jumpTo_label = $GLOBALS['TL_LANG']['WEM']['SMARTGEAR']['DEFAULT']['register'];
            $this->Template->wem_sg_login_register_jumpTo = $routeGenerator->generate($objTarget->name, [], UrlGeneratorInterface::ABSOLUTE_URL);
        }
    }
}
