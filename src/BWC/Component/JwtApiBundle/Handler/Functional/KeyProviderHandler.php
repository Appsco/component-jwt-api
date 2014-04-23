<?php

namespace BWC\Component\JwtApiBundle\Handler\Functional;

use BWC\Component\JwtApiBundle\Context\ContextOptions;
use BWC\Component\JwtApiBundle\Context\JwtContext;
use BWC\Component\JwtApiBundle\Handler\ContextHandlerInterface;
use BWC\Component\JwtApiBundle\KeyProvider\KeyProviderInterface;
use Psr\Log\LoggerInterface;


class KeyProviderHandler implements ContextHandlerInterface
{
    /** @var  KeyProviderInterface */
    protected $keyProvider;

    /** @var  LoggerInterface|null */
    protected $logger;


    /**
     * @param KeyProviderInterface $keyProvider
     * @param \Psr\Log\LoggerInterface|null $logger
     */
    public function __construct(KeyProviderInterface $keyProvider, LoggerInterface $logger = null)
    {
        $this->keyProvider = $keyProvider;
        $this->logger = $logger;
    }

    /**
     * @param \BWC\Component\JwtApiBundle\KeyProvider\KeyProviderInterface $keyProvider
     */
    public function setKeyProvider($keyProvider)
    {
        $this->keyProvider = $keyProvider;
    }

    /**
     * @return \BWC\Component\JwtApiBundle\KeyProvider\KeyProviderInterface
     */
    public function getKeyProvider()
    {
        return $this->keyProvider;
    }

    /**
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
    }

    /**
     * @return \Psr\Log\LoggerInterface
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
        $keys = $this->keyProvider->getKeys($context);
        if ($this->logger) {
            $this->logger->debug('KeyProviderHandler.keys', array('keys'=>$keys));
        }
        $context->optionSet(ContextOptions::KEYS, $keys);
    }

    /**
     * @return string
     */
    public function info()
    {
        return 'KeyProviderHandler';
    }


}
