<?php

namespace BWC\Component\JwtApiBundle\Handler\Functional;

use BWC\Component\JwtApiBundle\Context\ContextOptions;
use BWC\Component\JwtApiBundle\Context\JwtContext;
use BWC\Component\JwtApiBundle\Handler\ContextHandlerInterface;
use BWC\Component\JwtApiBundle\KeyProvider\KeyProviderInterface;


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