<?php

namespace BWC\Component\JwtApi\Handler\Functional;

use BWC\Component\JwtApi\Context\JwtContext;
use BWC\Component\JwtApi\Error\JwtException;
use BWC\Component\JwtApi\Handler\ContextHandlerInterface;
use BWC\Component\JwtApi\Method\Directions;
use BWC\Component\JwtApi\Method\MethodJwt;

class UnhandledContextHandler implements ContextHandlerInterface
{
    protected $myIssuerId;


    /**
     * @param string $myIssuerId
     */
    public function __construct($myIssuerId)
    {
        $this->myIssuerId = $myIssuerId;
    }



    /**
     * @param JwtContext $context
     * @throws \BWC\Component\JwtApi\Error\JwtException
     */
    public function handleContext(JwtContext $context)
    {
        $requestJwt = $context->getRequestJwt();
        if ($requestJwt->getDirection() == Directions::RESPONSE) {
            throw new JwtException('Unhandled response');
        }

        $responseJwt = MethodJwt::create(
            Directions::RESPONSE,
            $this->myIssuerId,
            $requestJwt->getMethod(),
            $requestJwt->getInstance(),
            null,
            $requestJwt->getJwtId()
        );

        $responseJwt->setException('Unhandled request');

        $context->setResponseJwt($responseJwt);
    }

} 