<?php

namespace BWC\Component\JwtApiBundle\Handler\Structural;

use BWC\Component\JwtApiBundle\Context\JwtContext;
use BWC\Component\JwtApiBundle\Handler\ContextHandlerInterface;

class NullContextHandler implements ContextHandlerInterface
{
    /**
     * @param JwtContext $context
     */
    public function handleContext(JwtContext $context)
    {

    }

} 