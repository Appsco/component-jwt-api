<?php

namespace BWC\Component\JwtApi\Handler\Structural;

use BWC\Component\JwtApi\Handler\ContextHandlerInterface;
use BWC\Component\JwtApi\Method\MethodClaims;

class DirectionMethodFilterHandler extends JwtPayloadFilterHandler
{
    public function __construct(ContextHandlerInterface $innerHandler, $direction, $method)
    {
        parent::__construct($innerHandler, array(
            MethodClaims::DIRECTION => $direction,
            MethodClaims::METHOD => $method
        ));
    }

} 