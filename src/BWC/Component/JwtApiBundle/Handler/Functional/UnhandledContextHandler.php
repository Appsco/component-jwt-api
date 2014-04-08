<?php

namespace BWC\Component\JwtApiBundle\Handler\Functional;

use BWC\Component\JwtApiBundle\Context\ContextOptions;
use BWC\Component\JwtApiBundle\Context\JwtContext;
use BWC\Component\JwtApiBundle\Error\JwtException;
use BWC\Component\JwtApiBundle\Handler\ContextHandlerInterface;
use BWC\Component\JwtApiBundle\Method\Directions;
use BWC\Component\JwtApiBundle\Method\MethodJwt;

class UnhandledContextHandler implements ContextHandlerInterface
{
    /**
     * @param JwtContext $context
     * @throws \BWC\Component\JwtApiBundle\Error\JwtException
     */
    public function handleContext(JwtContext $context)
    {
        if ($context->getResponseJwt() || $context->optionGet(ContextOptions::HANDLED)) {
            return;
        }

        $requestJwt = $context->getRequestJwt();
        if ($requestJwt->getDirection() == Directions::RESPONSE) {
            throw new JwtException('Unhandled response');
        }

        $responseJwt = MethodJwt::create(
            Directions::RESPONSE,
            $context->getMyIssuerId(),
            $requestJwt->getMethod(),
            $requestJwt->getInstance(),
            null,
            $requestJwt->getJwtId()
        );

        $responseJwt->setException('Unhandled request');

        $context->setResponseJwt($responseJwt);
    }

    /**
     * @return string
     */
    public function info()
    {
        return 'UnhandledContextHandler';
    }


} 