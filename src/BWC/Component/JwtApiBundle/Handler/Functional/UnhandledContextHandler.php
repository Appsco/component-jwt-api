<?php

namespace BWC\Component\JwtApiBundle\Handler\Functional;

use BWC\Component\JwtApiBundle\Context\JwtContext;
use BWC\Component\JwtApiBundle\Error\JwtException;
use BWC\Component\JwtApiBundle\Handler\ContextHandlerInterface;
use BWC\Component\JwtApiBundle\Method\Directions;
use BWC\Component\JwtApiBundle\Method\MethodJwt;

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
     * @throws \BWC\Component\JwtApiBundle\Error\JwtException
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