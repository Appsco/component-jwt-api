<?php

namespace BWC\Component\JwtApi\Handler\Functional;

use BWC\Component\Jwe\Jose;
use BWC\Component\JwtApi\Context\ContextOptions;
use BWC\Component\JwtApi\Context\JwtContext;
use BWC\Component\JwtApi\Error\JwtException;
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
        $jwt = $context->getRequestJwt();
        if (false == $jwt instanceof Jose) {
            throw new JwtException('Missing jwt to validate');
        }

        $keys = $context->optionGet(ContextOptions::KEYS);
        if (false == is_array($keys)) {
            throw new JwtException('Expected array of strings to validate jwt with');
        }

        $this->validator->validate($jwt, $keys);
    }

} 