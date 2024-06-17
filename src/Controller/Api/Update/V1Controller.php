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
use Symfony\Component\Routing\Annotation\Route;
use Terminal42\ServiceAnnotationBundle\Annotation\ServiceTag;
use WEM\SmartgearBundle\Api\Update\V1\Api;
use Contao\CoreBundle\Framework\ContaoFramework;
use WEM\SmartgearBundle\Classes\Api\Security\Token;
use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\SmartgearBundle\Exceptions\Api\InvalidTokenException;

#[Route(path: '/api/update/v1')]
#[ServiceTag(["controller.service_arguments"])]
class V1Controller extends Controller
{

    public function __construct(
        protected ContaoFramework     $framework,
        protected TranslatorInterface $translator,
        protected Api                 $api,
        protected Token               $securityToken
    )
    {
        $this->framework->initialize();
        parent::__construct();
    }

    #[Route(path: '/list', methods: ['GET'])]
    public function listAction(Request $request): Response
    {
        try{
            $this->validateToken($request);
            return new Response(
                $this->api->list()->toJson(),
                200,
                ['Content-Type'=>'application/json']
             );
        }catch(\Exception $exception){
            return new Response(json_encode(['message'=>$exception->getMessage()]), 400,['Content-Type'=>'application/json']);
        }
    }

    #[Route(path: '/update', methods: ['POST'])]
    public function updateAction(Request $request): Response
    {
        try{
            $this->validateToken($request);
            return new Response(
                $this->api->update($request->query->getBoolean('nobackup'))->toJson(),
                200,
                ['Content-Type'=>'application/json']
             );
        }catch(\Exception $exception){
            return new Response(json_encode(['message'=>$exception->getMessage()]), 400,['Content-Type'=>'application/json']);
        }
    }

    /**
     * @throws InvalidTokenException
     */
    protected function validateToken(Request $request): void
    {
        if(!$this->securityToken->validate($request->query->get('token'))){
            throw new InvalidTokenException($this->translator->trans('WEM.SMARTGEAR.DEFAULT.InvalidToken', [], 'contao_default'));
        }
    }
}
