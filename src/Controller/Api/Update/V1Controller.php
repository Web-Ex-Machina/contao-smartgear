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
use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\SmartgearBundle\Exceptions\Api\InvalidTokenException;

/**
 * @Route("/api/update/v1")
 * @ServiceTag("controller.service_arguments")
 */
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
                $this->api->update($request->query->getBoolean('nobackup'))->toJson(),
                200,
                ['Content-Type'=>'application/json']
             );
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
