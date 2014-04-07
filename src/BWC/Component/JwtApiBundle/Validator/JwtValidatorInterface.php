<?php

namespace BWC\Component\JwtApiBundle\Validator;

use BWC\Component\JwtApiBundle\Context\JwtContext;


interface JwtValidatorInterface
{
    /**
     * @param \BWC\Component\JwtApiBundle\Context\JwtContext $context
     * @throws \BWC\Component\JwtApiBundle\Error\JwtException
     */
    public function validate(JwtContext $context);

} 