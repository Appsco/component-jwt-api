<?php

namespace BWC\Component\JwtApi\Handler;


use BWC\Component\JwtApi\Context\JwtContext;

interface ContextHandlerInterface
{
    /**
     * @param JwtContext $context
     */
    public function handleContext(JwtContext $context);

} 