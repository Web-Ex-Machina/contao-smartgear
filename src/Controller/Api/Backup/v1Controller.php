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

namespace WEM\SmartgearBundle\Controller\Api\Backup;

use Contao\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Terminal42\ServiceAnnotationBundle\Annotation\ServiceTag;
use WEM\SmartgearBundle\Backup\BackupManager;

/**
 * @Route("/api/backup/v1")
 * @ServiceTag("controller.service_arguments")
 */
class v1Controller extends Controller
{
    /** @var BackupManager */
    protected $backupManager;

    public function __construct(BackupManager $backupManager)
    {
        $this->backupManager = $backupManager;
    }

    /**
     * @Route("/")
     *
     * @param Request $request Current request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        return new Response('INDEX BACKUP V1!');
    }
}
