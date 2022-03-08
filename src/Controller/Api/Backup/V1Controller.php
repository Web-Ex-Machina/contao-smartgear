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
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Terminal42\ServiceAnnotationBundle\Annotation\ServiceTag;
use WEM\SmartgearBundle\Api\Backup\V1\Api;
use Contao\CoreBundle\Framework\ContaoFramework;
use WEM\SmartgearBundle\Classes\Api\Security\Token;

/**
 * @Route("/api/backup/v1")
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
                $this->api->list(
                    $request->query->getInt('limit',10),
                    $request->query->getInt('offset',0),
                    -1 === $request->query->getInt('before',-1) ? null : $request->query->getInt('before'),
                    -1 === $request->query->getInt('after',-1) ? null : $request->query->getInt('after')
                )->toJson(),
                200,
                ['Content-Type'=>'application/json']
             );
        }catch(\Exception $e){
            return new Response(json_encode(['message'=>$e->getMessage()]), 400,['Content-Type'=>'application/json']);
        }

    }

    /**
     * @Route("/create", methods={"POST"})
     *
     * @param Request $request Current request
     *
     * @return Response
     */
    public function createAction(Request $request)
    {
        try{
            $this->validateToken($request);
            return new Response(
                $this->api->create()->toJson(),
                200,
                ['Content-Type'=>'application/json']
             );
        }catch(\Exception $e){
            return new Response(json_encode(['message'=>$e->getMessage()]), 400,['Content-Type'=>'application/json']);
        }

    }

    /**
     * @Route("/delete/{backupname}", methods={"POST"})
     *
     * @param Request $request Current request
     *
     * @return Response
     */
    public function deleteAction(Request $request, string $backupname)
    {
        try{
            $this->validateToken($request);
            return new Response(
                $this->api->delete($backupname),
                200,
                ['Content-Type'=>'application/json']
             );
        }catch(\Exception $e){
            return new Response(json_encode(['message'=>$e->getMessage()]), 400,['Content-Type'=>'application/json']);
        }
    }

    /**
     * @Route("/restore/{backupname}", methods={"POST"})
     *
     * @param Request $request Current request
     *
     * @return Response
     */
    public function restoreAction(Request $request, string $backupname)
    {
        try{
            $this->validateToken($request);
            return new Response(
                $this->api->restore($backupname),
                200,
                ['Content-Type'=>'application/json']
             );
        }catch(\Exception $e){
            return new Response(json_encode(['message'=>$e->getMessage()]), 400,['Content-Type'=>'application/json']);
        }
    }

    /**
     * @Route("/get/{backupname}", methods={"GET"})
     *
     * @param Request $request Current request
     *
     * @return Response
     */
    public function getAction(Request $request, string $backupname)
    {
        try{
            $this->validateToken($request);
            $file = $this->api->get($backupname);
            $response = new BinaryFileResponse($file->path);
            $response->headers->set('Content-Type', 'application/zip');
            $response->setContentDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                $file->basename
            );
            $response->setStatusCode(200);
            return $response;
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
