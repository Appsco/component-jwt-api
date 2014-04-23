<?php

namespace BWC\Component\JwtApiBundle\Handler\Functional;

use BWC\Component\JwtApiBundle\Bearer\BearerProviderInterface;
use BWC\Component\JwtApiBundle\Context\JwtContext;
use BWC\Component\JwtApiBundle\Handler\ContextHandlerInterface;
use Psr\Log\LoggerInterface;


class BearerProviderHandler implements ContextHandlerInterface
{
    /** @var  BearerProviderInterface */
    protected $bearerProvider;

    /** @var  LoggerInterface|null */
    protected $logger;


    /**
     * @param BearerProviderInterface $bearerProvider
     * @param \Psr\Log\LoggerInterface|null $logger
     */
    public function __construct(BearerProviderInterface $bearerProvider, LoggerInterface $logger = null)
    {
        $this->bearerProvider = $bearerProvider;
        $this->logger = $logger;
    }

    /**
     * @param \BWC\Component\JwtApiBundle\Bearer\BearerProviderInterface $bearerProvider
     */
    public function setBearerProvider($bearerProvider)
    {
        $this->bearerProvider = $bearerProvider;
    }

    /**
     * @return \BWC\Component\JwtApiBundle\Bearer\BearerProviderInterface
     */
    public function getBearerProvider()
    {
        return $this->bearerProvider;
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
        $bearer = $this->bearerProvider->getBearer($context);
        if ($this->logger) {
            $this->logger->debug('BearerProviderHandler.bearer', array('bearer'=>$bearer));
        }
        $context->setBearer($bearer);
    }

    /**
     * @return string
     */
    public function info()
    {
        return 'BearerProviderHandler';
    }


} 