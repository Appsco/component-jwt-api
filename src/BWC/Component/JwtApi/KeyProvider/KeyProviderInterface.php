<?php

namespace BWC\Component\JwtApi\KeyProvider;

use BWC\Component\JwtApi\Context\JwtContext;

interface KeyProviderInterface
{
    /**
     * @param JwtContext $context
     * @return string[]
     */
    public function getKeys(JwtContext $context);

} 