<?php

namespace BWC\Component\JwtApiBundle\Strategy\Exception;

use BWC\Component\JwtApiBundle\Context\JwtContext;
use BWC\Component\JwtApiBundle\Method\Directions;
use BWC\Component\JwtApiBundle\Method\MethodJwt;

class SetToResponseJwt implements ExceptionStrategyInterface
{
    /**
     * @param \Exception $exception
     * @param JwtContext $context
     * @return void
     */
    public function handle(\Exception $exception, JwtContext $context)
    {
        $requestJwt = $context->getRequestJwt();
        if (!$requestJwt || $requestJwt->getDirection() == Directions::RESPONSE) {
            return;
        }

        $responseJwt = MethodJwt::create(
            Directions::RESPONSE,
            $context->getMyIssuerId(),
            $requestJwt->getMethod(),
            $requestJwt->getInstance(),
            null,
            $requestJwt->getJwtId()
        );

        $responseJwt->setException($exception->getMessage());

        $context->setResponseJwt($responseJwt);
    }

} 