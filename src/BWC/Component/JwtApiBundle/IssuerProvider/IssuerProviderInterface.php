<?php

namespace BWC\Component\JwtApiBundle\IssuerProvider;

use BWC\Component\JwtApiBundle\Context\JwtContext;


interface IssuerProviderInterface
{
    /**
     * @param JwtContext $context
     * @return string
     */
    public function getIssuer(JwtContext $context);
} 