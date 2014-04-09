<?php

namespace BWC\Component\JwtApiBundle\Strategy\Exception;

use BWC\Component\JwtApiBundle\Context\JwtContext;
use Psr\Log\LoggerInterface;


class Logger implements ExceptionStrategyInterface
{
    /** @var  LoggerInterface */
    protected $logger;


    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param \Exception $exception
     * @param JwtContext $context
     * @return void
     */
    public function handle(\Exception $exception, JwtContext $context)
    {
        $this->logger->error((string)$exception, array(
            'context' => $context->jsonSerialize(),
            'clientIps' => $context->getRequest()->getClientIps(),
        ));
    }

} 