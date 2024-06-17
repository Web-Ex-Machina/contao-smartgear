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

namespace WEM\SmartgearBundle\Controller\Api\Smartgear;

use Contao\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Terminal42\ServiceAnnotationBundle\Annotation\ServiceTag;
use Exception;
use WEM\SmartgearBundle\Api\Smartgear\V1\Api;
use WEM\SmartgearBundle\Classes\Api\Security\ApiKey;
use WEM\SmartgearBundle\Classes\Api\Security\Token;
use Symfony\Contracts\Translation\TranslatorInterface;
use WEM\SmartgearBundle\Exceptions\Api\InvalidTokenException;

#[Route(path: '/api/smartgear/v1')]
#[ServiceTag(["controller.service_arguments"])]
class V1Controller extends Controller
{
    public function __construct(
        protected TranslatorInterface $translator,
        protected Api $api,
        protected ApiKey $securityApiKey,
        protected Token $securityToken
    )
    {
        parent::__construct();
    }

    /**
     *
     * @param Request $request Current request
     */
    #[Route(path: '/token', methods: ['GET'])]
    public function tokenAction(Request $request): Response
    {
        try{
            if(!$this->securityApiKey->validate($request->query->get('apikey'))){
                throw new Exception("Api keys don't match");
            }

            // define token and return it
            $tokenResponse = $this->api->token();

        }catch(\Exception $exception){
            return new Response(json_encode(['message'=>$exception->getMessage()]), 400,['Content-Type'=>'application/json']);
        }

        return new Response($tokenResponse, 200,['Content-Type'=>'application/json']);
    }

    #[Route(path: '/version', methods: ['GET'])]
    public function versionAction(Request $request): Response
    {
        try{
            $this->validateToken($request);

            $versionResponse = $this->api->version();
        }catch(\Exception $exception){
            return new Response(json_encode(['message'=>$exception->getMessage()]), 400,['Content-Type'=>'application/json']);
        }

        return new Response($versionResponse, 200,['Content-Type'=>'application/json']);
    }

    protected function validateToken(Request $request): void
    {
        if(!$this->securityToken->validate($request->query->get('token'))){
            throw new InvalidTokenException($this->translator->trans('WEM.SMARTGEAR.DEFAULT.InvalidToken', [], 'contao_default'));
        }
    }
}