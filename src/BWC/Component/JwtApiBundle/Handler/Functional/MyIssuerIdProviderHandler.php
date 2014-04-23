<?php

namespace BWC\Component\JwtApiBundle\Handler\Functional;

use BWC\Component\JwtApiBundle\Context\JwtContext;
use BWC\Component\JwtApiBundle\Handler\ContextHandlerInterface;
use BWC\Component\JwtApiBundle\IssuerProvider\IssuerProviderInterface;
use Psr\Log\LoggerInterface;


class MyIssuerIdProviderHandler implements ContextHandlerInterface
{
    /** @var  IssuerProviderInterface */
    protected $issuerProvider;

    /** @var  LoggerInterface|null */
    protected $logger;


    /**
     * @param IssuerProviderInterface $issuerProvider
     * @param \Psr\Log\LoggerInterface|null $logger
     */
    public function __construct(IssuerProviderInterface $issuerProvider, LoggerInterface $logger = null)
    {
        $this->issuerProvider = $issuerProvider;
        $this->logger = $logger;
    }



    /**
     * @param \BWC\Component\JwtApiBundle\IssuerProvider\IssuerProviderInterface $issuerProvider
     */
    public function setIssuerProvider($issuerProvider)
    {
        $this->issuerProvider = $issuerProvider;
    }

    /**
     * @return \BWC\Component\JwtApiBundle\IssuerProvider\IssuerProviderInterface
     */
    public function getIssuerProvider()
    {
        return $this->issuerProvider;
    }

    /**
     * @param null|\Psr\Log\LoggerInterface $logger
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
    }

    /**
     * @return null|\Psr\Log\LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }



    /**
     * @param JwtContext $context
     */
    public function handleContext(JwtContext $context)
    {
        $issuer = $this->issuerProvider->getIssuer($context);

        if ($this->logger) {
            $this->logger->debug('MyIssuerIdProviderHandler', array('issuer'=>$issuer));
        }

        $context->setMyIssuerId($issuer);
    }

    /**
     * @return string
     */
    public function info()
    {
        return 'MyIssuerIdProviderHandler';
    }


} 