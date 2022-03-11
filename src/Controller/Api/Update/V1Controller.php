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

namespace WEM\SmartgearBundle\Controller\Api\Update;

use Contao\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Terminal42\ServiceAnnotationBundle\Annotation\ServiceTag;
use WEM\SmartgearBundle\Api\Update\V1\Api;
use Contao\CoreBundle\Framework\ContaoFramework;
use WEM\SmartgearBundle\Classes\Api\Security\Token;

/**
 * @Route("/api/update/v1")
 * @ServiceTag("controller.service_arguments")
 */
class V1Controller extends Controller
{
    /** @var Api */
    protected $api;
    protected ContaoFramework $framework;
    protected Token $securityToken;

    public function __construct(
        ContaoFramework $framework, 
        Api $api,
        Token $securityToken
    )
    {
        $this->framework = $framework;
        $this->api = $api;
        $this->securityToken = $securityToken;
        $this->framework->initialize();
    }

    /**
     * @Route("/list", methods={"GET"})
     *
     * @param Request $request Current request
     *
     * @return Response
     */
    public function listAction(Request $request)
    {
        try{
            $this->validateToken($request);
            return new Response(
                $this->api->list()->toJson(),
                200,
                ['Content-Type'=>'application/json']
             );
        }catch(\Exception $e){
            return new Response(json_encode(['message'=>$e->getMessage()]), 400,['Content-Type'=>'application/json']);
        }
    }

    /**
     * @Route("/update", methods={"POST"})
     *
     * @param Request $request Current request
     *
     * @return Response
     */
    public function updateAction(Request $request)
    {
        try{
            $this->validateToken($request);
            return new Response(
                $this->api->update()->toJson(),
                200,
                ['Content-Type'=>'application/json']
             );
        }catch(\Exception $e){
            return new Response(json_encode(['message'=>$e->getMessage()]), 400,['Content-Type'=>'application/json']);
        }
    }

    protected function validateToken(Request $request): void
    {
        // dump($request->query->get('token'));
        if(!$this->securityToken->validate($request->query->get('token'))){
            throw new \Exception('Invalid token "' .$request->query->get('token'). '"');
        }
    }
}
