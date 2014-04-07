<?php

namespace BWC\Component\JwtApiBundle\Handler\Functional;

use BWC\Component\JwtApiBundle\Context\JwtContext;
use BWC\Component\JwtApiBundle\Handler\ContextHandlerInterface;
use BWC\Component\JwtApiBundle\Validator\JwtValidatorInterface;


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
     * @throws \BWC\Component\JwtApiBundle\Error\JwtException
     */
    public function handleContext(JwtContext $context)
    {
        $this->validator->validate($context);
    }

} 