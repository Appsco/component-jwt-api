<?php

namespace BWC\Component\JwtApi\Handler\Functional;

use BWC\Component\JwtApi\Context\ContextOptions;
use BWC\Component\JwtApi\Context\JwtContext;
use BWC\Component\JwtApi\Handler\ContextHandlerInterface;
use BWC\Component\JwtApi\KeyProvider\KeyProviderInterface;


class KeyProviderHandler implements ContextHandlerInterface
{
    /** @var  KeyProviderInterface */
    protected $keyProvider;


    /**
     * @param KeyProviderInterface $keyProvider
     */
    public function __construct(KeyProviderInterface $keyProvider)
    {
        $this->keyProvider = $keyProvider;
    }



    /**
     * @param JwtContext $context
     */
    public function handleContext(JwtContext $context)
    {
        $context->optionSet(ContextOptions::KEYS, $this->keyProvider->getKeys($context));
    }

} 