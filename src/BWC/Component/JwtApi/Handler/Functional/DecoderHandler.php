<?php

namespace BWC\Component\JwtApi\Handler\Functional;

use BWC\Component\Jwe\EncoderInterface;
use BWC\Component\JwtApi\Context\JwtContext;
use BWC\Component\JwtApi\Error\JwtException;
use BWC\Component\JwtApi\Handler\ContextHandlerInterface;
use BWC\Component\JwtApi\Method\MethodJwt;


class DecoderHandler implements ContextHandlerInterface
{
    /** @var  EncoderInterface */
    protected $encoder;

    /** @var  string */
    protected $class;


    /**
     * @param EncoderInterface $encoder
     * @param string $class
     */
    public function __construct(EncoderInterface $encoder, $class = '\BWC\Component\JwtApi\Method\MethodJwt')
    {
        $this->encoder = $encoder;
        $this->class = $class;
    }


    /**
     * @param JwtContext $context
     * @throws \BWC\Component\JwtApi\Error\JwtException
     */
    public function handleContext(JwtContext $context)
    {
        $token = $context->getRequestJwtToken();
        /** @var MethodJwt $jwt */
        $jwt = $this->encoder->decode($token, $this->class);

        if (false == $jwt instanceof MethodJwt) {
            throw new JwtException(sprintf("Expected MethodJwt but got '%s'", get_class($jwt)));
        }

        $context->setRequestJwt($jwt);
    }

} 