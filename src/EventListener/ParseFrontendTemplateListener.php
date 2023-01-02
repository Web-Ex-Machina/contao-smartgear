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

use Contao\FrontendTemplate;
use WEM\SmartgearBundle\Classes\Config\Manager\ManagerJson as CoreConfigurationManager;

class ParseFrontendTemplateListener
{
    /** @var CoreConfigurationManager */
    protected $configurationManager;

    public function __construct(
        CoreConfigurationManager $configurationManager
    ) {
        $this->configurationManager = $configurationManager;
    }

    public function __invoke(string $buffer, string $templateName, FrontendTemplate $template): string
    {
        return $this->manageBreadcrumbBehaviour($buffer, $templateName, $template);
    }

    protected function manageBreadcrumbBehaviour(string $buffer, string $templateName, FrontendTemplate $template): string
    {
        // Check if we want the breadcrumb to be automatically managed

        if (!\array_key_exists('WEMSG', $_SESSION)
        || !\array_key_exists('ParseFrontendTemplateListener', $_SESSION['WEMSG'])
        ) {
            $_SESSION['WEMSG']['ParseFrontendTemplateListener'] = [
                'current_tpl_index' => 0,
                'mod_breadcrumb' => [
                    'index' => null,
                    'buffer' => null,
                ],
                'fe_page' => [
                    'index' => null,
                ],
                'mod_after_breadcrumb' => [
                    'index' => null,
                    'buffer' => null,
                    'template' => null,
                ],
            ];
        }

        if ('fe_page' === $templateName) {
            $_SESSION['WEMSG']['ParseFrontendTemplateListener']['fe_page']['index'] = $_SESSION['WEMSG']['ParseFrontendTemplateListener']['current_tpl_index'];
            // Modify $buffer
            if (null !== $_SESSION['WEMSG']['ParseFrontendTemplateListener']['mod_breadcrumb']['buffer']
            && null !== $_SESSION['WEMSG']['ParseFrontendTemplateListener']['mod_after_breadcrumb']['buffer']
            ) {
                // Check if the mod_after_breadcrumb_template is one
                // needing a replacement (breadcrumb config somewhere ?)

                $buffer = str_replace($_SESSION['WEMSG']['ParseFrontendTemplateListener']['mod_breadcrumb']['buffer'], '', $buffer);
                $buffer = str_replace($_SESSION['WEMSG']['ParseFrontendTemplateListener']['mod_after_breadcrumb']['buffer'], $_SESSION['WEMSG']['ParseFrontendTemplateListener']['mod_after_breadcrumb']['buffer'].$_SESSION['WEMSG']['ParseFrontendTemplateListener']['mod_breadcrumb']['buffer'], $buffer);
            }
        } elseif ('mod_breadcrumb' === $templateName) {
            $_SESSION['WEMSG']['ParseFrontendTemplateListener']['mod_breadcrumb']['index'] = $_SESSION['WEMSG']['ParseFrontendTemplateListener']['current_tpl_index'];
            $_SESSION['WEMSG']['ParseFrontendTemplateListener']['mod_breadcrumb']['buffer'] = $buffer;
        } else {
            if (null !== $_SESSION['WEMSG']['ParseFrontendTemplateListener']['mod_breadcrumb']['index']
            && $_SESSION['WEMSG']['ParseFrontendTemplateListener']['current_tpl_index'] === $_SESSION['WEMSG']['ParseFrontendTemplateListener']['mod_breadcrumb']['index'] + 1) {
                $_SESSION['WEMSG']['ParseFrontendTemplateListener']['mod_after_breadcrumb']['index'] = $_SESSION['WEMSG']['ParseFrontendTemplateListener']['current_tpl_index'];
                $_SESSION['WEMSG']['ParseFrontendTemplateListener']['mod_after_breadcrumb']['buffer'] = $buffer;
                $_SESSION['WEMSG']['ParseFrontendTemplateListener']['mod_after_breadcrumb']['template'] = $templateName;
            }
        }
        ++$_SESSION['WEMSG']['ParseFrontendTemplateListener']['current_tpl_index'];

        return $buffer;
    }
}
