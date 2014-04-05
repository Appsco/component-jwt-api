<?php

namespace BWC\Component\JwtApi\Handler\Functional;

use BWC\Component\JwtApi\Bearer\BearerProviderInterface;
use BWC\Component\JwtApi\Context\JwtContext;
use BWC\Component\JwtApi\Handler\ContextHandlerInterface;


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