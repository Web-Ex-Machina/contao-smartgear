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

namespace WEM\SmartgearBundle\Classes;

use Contao\Form;
use Contao\Model;
use Contao\PageModel;

class FormUtil
{
    public static function getPageFromForm(Form $form): ?PageModel
    {
        $objParent = $form->getParent();
        $model = Model::getClassFromTable($objParent->ptable);
        $objGreatParent = $model::findOneById($objParent->pid);

        return PageModel::findOneById($objGreatParent->pid);
    }
}
