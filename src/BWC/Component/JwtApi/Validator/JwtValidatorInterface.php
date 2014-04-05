<?php

namespace BWC\Component\JwtApi\Validator;

use BWC\Component\JwtApi\Context\JwtContext;


interface JwtValidatorInterface
{
    /**
     * @param \BWC\Component\JwtApi\Context\JwtContext $context
     * @throws \BWC\Component\JwtApi\Error\JwtException
     */
    public function validate(JwtContext $context);

} 