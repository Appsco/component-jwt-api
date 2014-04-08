<?php

namespace BWC\Component\JwtApiBundle\Handler\Functional;

use BWC\Component\Jwe\EncoderInterface;
use BWC\Component\JwtApiBundle\Context\ContextOptions;
use BWC\Component\JwtApiBundle\Context\JwtContext;
use BWC\Component\JwtApiBundle\Handler\ContextHandlerInterface;

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

    /**
     * @return string
     */
    public function info()
    {
        return 'EncoderHandler';
    }


} 