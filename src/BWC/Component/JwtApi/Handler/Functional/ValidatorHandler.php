<?php

namespace BWC\Component\JwtApi\Handler\Functional;

use BWC\Component\JwtApi\Context\JwtContext;
use BWC\Component\JwtApi\Handler\ContextHandlerInterface;
use BWC\Component\JwtApi\Validator\JwtValidatorInterface;


class ValidatorHandler implements ContextHandlerInterface
{
    /** @var  JwtValidatorInterface */
    protected $validator;


    public function __construct(JwtValidatorInterface $validator)
    {
        $this->validator = $validator;
    }


    /**
     * @param JwtContext $context
     * @throws \BWC\Component\JwtApi\Error\JwtException
     */
    public function handleContext(JwtContext $context)
    {
        $this->validator->validate($context);
    }

} 