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
use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\SmartgearBundle\Exceptions\Api\InvalidTokenException;

#[Route(path: '/api/backup/v1')]
#[ServiceTag(["controller.service_arguments"])]
class V1Controller extends Controller
{
    /** @var TranslatorInterface */
    protected $translator;
    /** @var Api */
    protected $api;

    public function __construct(
        protected ContaoFramework $framework, 
        TranslatorInterface $translator,
        Api $api,
        protected Token $securityToken
    )
    {
        $this->translator = $translator;
        $this->api = $api;
        $this->framework->initialize();
    }

    /**
     *
     * @param Request $request Current request
     * @return Response
     */
    #[Route(path: '/list', methods: ['GET'])]
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
     *
     * @param Request $request Current request
     * @return Response
     */
    #[Route(path: '/create', methods: ['POST'])]
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
     *
     * @param Request $request Current request
     * @return Response
     */
    #[Route(path: '/delete/{backupname}', methods: ['POST'])]
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
     *
     * @param Request $request Current request
     * @return Response
     */
    #[Route(path: '/restore/{backupname}', methods: ['POST'])]
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
     *
     * @param Request $request Current request
     * @return Response
     */
    #[Route(path: '/get/{backupname}', methods: ['GET'])]
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
        if(!$this->securityToken->validate($request->query->get('token'))){
            throw new InvalidTokenException($this->translator->trans('WEM.SMARTGEAR.DEFAULT.InvalidToken', [], 'contao_default'));
        }
    }
}
