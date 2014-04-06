<?php

namespace BWC\Component\JwtApi\Handler\Structural;

use BWC\Component\JwtApi\Context\JwtContext;
use BWC\Component\JwtApi\Handler\ContextHandlerInterface;

class NullContextHandler implements ContextHandlerInterface
{
    /**
     * @param JwtContext $context
     */
    public function handleContext(JwtContext $context)
    {

    }

} 