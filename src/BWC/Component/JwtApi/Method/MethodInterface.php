<?php

namespace BWC\Component\JwtApi\Method;

use BWC\Component\JwtApi\Context\JwtContext;


interface MethodInterface
{
    /**
     * @param JwtContext $context
     */
    public function handle(JwtContext $context);

} 