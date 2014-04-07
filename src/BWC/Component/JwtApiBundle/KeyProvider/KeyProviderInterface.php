<?php

namespace BWC\Component\JwtApiBundle\KeyProvider;

use BWC\Component\JwtApiBundle\Context\JwtContext;


interface KeyProviderInterface
{
    /**
     * @param JwtContext $context
     * @return string[]
     */
    public function getKeys(JwtContext $context);

} 