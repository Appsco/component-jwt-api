<?php

namespace BWC\Component\JwtApiBundle\Handler\Functional;

use BWC\Component\JwtApiBundle\Bearer\BearerProviderInterface;
use BWC\Component\JwtApiBundle\Context\JwtContext;
use BWC\Component\JwtApiBundle\Handler\ContextHandlerInterface;


class BearerProviderHandler implements ContextHandlerInterface
{
    /** @var  BearerProviderInterface */
    protected $bearerProvider;


    /**
     * @param BearerProviderInterface $bearerProvider
     */
    public function __construct(BearerProviderInterface $bearerProvider)
    {
        $this->bearerProvider = $bearerProvider;
    }


    /**
     * @param JwtContext $context
     */
    public function handleContext(JwtContext $context)
    {
        $context->setBearer($this->bearerProvider->getBearer($context));
    }

} 