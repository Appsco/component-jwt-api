<?php

namespace BWC\Component\JwtApi;

use BWC\Component\JwtApi\Context\JwtContext;

interface HandlerInterface
{
    /**
     * @param JwtContext $context
     */
    public function handle(JwtContext $context);

} 