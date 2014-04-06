<?php

namespace BWC\Component\JwtApi\Handler\Functional;

use BWC\Component\Jwe\EncoderInterface;
use BWC\Component\JwtApi\Context\ContextOptions;
use BWC\Component\JwtApi\Context\JwtContext;
use BWC\Component\JwtApi\Handler\ContextHandlerInterface;

class EncoderHandler implements ContextHandlerInterface
{
    /** @var  EncoderInterface */
    protected $encoder;



    /**
     * @param EncoderInterface $encoder
     */
    public function __construct(EncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }



    /**
     * @param JwtContext $context
     */
    public function handleContext(JwtContext $context)
    {
        if ($context->getResponseJwt()) {
            $keys = $context->optionGet(ContextOptions::KEYS);
            if (is_array($keys)) {
                $context->setResponseToken(
                    $this->encoder->encode($context->getResponseJwt(), array_shift($keys))
                );
            }
        }
    }

} 