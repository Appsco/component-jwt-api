<?php

namespace BWC\Component\JwtApiBundle\Handler\Functional;

use BWC\Component\JwtApiBundle\Context\JwtContext;
use BWC\Component\JwtApiBundle\Handler\ContextHandlerInterface;
use BWC\Component\JwtApiBundle\IssuerProvider\IssuerProviderInterface;


class MyIssuerIdProviderHandler implements ContextHandlerInterface
{
    /** @var  IssuerProviderInterface */
    protected $issuerProvider;


    /**
     * @param IssuerProviderInterface $issuerProvider
     */
    public function __construct(IssuerProviderInterface $issuerProvider)
    {
        $this->issuerProvider = $issuerProvider;
    }


    /**
     * @param JwtContext $context
     */
    public function handleContext(JwtContext $context)
    {
        $context->setMyIssuerId($this->issuerProvider->getIssuer($context));
    }

    /**
     * @return string
     */
    public function info()
    {
        return 'MyIssuerIdProviderHandler';
    }


} 