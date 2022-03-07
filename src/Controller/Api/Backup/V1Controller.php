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
use WEM\SmartgearBundle\Api\Backup\V1\Api;
use Contao\CoreBundle\Framework\ContaoFramework;

/**
 * @Route("/api/backup/v1")
 * @ServiceTag("controller.service_arguments")
 */
class V1Controller extends Controller
{
    /** @var Api */
    protected $api;
    protected ContaoFramework $framework;

    public function __construct(ContaoFramework $framework, Api $api)
    {
        $this->framework = $framework;
        $this->api = $api;
        $this->framework->initialize();
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
        // dump($request);

        return new Response(json_encode($this->api->list(10, 0)));
    }
}
