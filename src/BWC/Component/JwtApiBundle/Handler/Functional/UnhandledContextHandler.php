<?php

namespace BWC\Component\JwtApiBundle\Handler\Functional;

use BWC\Component\JwtApiBundle\Context\ContextOptions;
use BWC\Component\JwtApiBundle\Context\JwtContext;
use BWC\Component\JwtApiBundle\Error\JwtException;
use BWC\Component\JwtApiBundle\Handler\ContextHandlerInterface;
use BWC\Component\JwtApiBundle\Method\Directions;
use BWC\Component\JwtApiBundle\Method\MethodJwt;
use Psr\Log\LoggerInterface;

class UnhandledContextHandler implements ContextHandlerInterface
{
    /** @var  LoggerInterface|null */
    protected $logger;


    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger = null)
    {
        $this->logger = $logger;
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
     * @throws \BWC\Component\JwtApiBundle\Error\JwtException
     */
    public function handleContext(JwtContext $context)
    {
        if ($context->getResponseJwt() || $context->optionGet(ContextOptions::HANDLED)) {
            return;
        }

        if ($this->logger) {
            $this->logger->debug('UnhandledContextHandler', array('context'=>$context));
        }

        $message = sprintf("Unhandled request for direction '%s' method '%s' of issuer '%s'",
            $context->getRequestJwt()->getDirection(),
            $context->getRequestJwt()->getMethod(),
            $context->getRequestJwt()->getIssuer()
        );

        $requestJwt = $context->getRequestJwt();
        if ($requestJwt->getDirection() == Directions::RESPONSE) {
            throw new JwtException($message);
        }

        $responseJwt = MethodJwt::create(
            Directions::RESPONSE,
            $context->getMyIssuerId(),
            $requestJwt->getMethod(),
            $requestJwt->getInstance(),
            null,
            $requestJwt->getJwtId()
        );

        $responseJwt->setException($message);

        $context->setResponseJwt($responseJwt);
    }

    /**
     * @return string
     */
    public function info()
    {
        return 'UnhandledContextHandler';
    }


} 