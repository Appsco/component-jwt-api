<?php

namespace BWC\Component\JwtApiBundle\Handler;

use BWC\Component\JwtApiBundle\Context\JwtContext;

interface ContextHandlerInterface
{
    /**
     * @param JwtContext $context
     */
    public function handleContext(JwtContext $context);

    /**
     * @return string
     */
    public function info();
} 