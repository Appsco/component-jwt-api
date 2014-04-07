<?php

namespace BWC\Component\JwtApiBundle\Handler\Structural;

use BWC\Component\JwtApiBundle\Handler\ContextHandlerInterface;
use BWC\Component\JwtApiBundle\Method\MethodClaims;

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