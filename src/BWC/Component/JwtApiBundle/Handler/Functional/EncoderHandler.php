<?php

namespace BWC\Component\JwtApiBundle\Handler\Functional;

use BWC\Component\Jwe\EncoderInterface;
use BWC\Component\JwtApiBundle\Context\ContextOptions;
use BWC\Component\JwtApiBundle\Context\JwtContext;
use BWC\Component\JwtApiBundle\Handler\ContextHandlerInterface;
use Psr\Log\LoggerInterface;

class EncoderHandler implements ContextHandlerInterface
{
    /** @var  EncoderInterface */
    protected $encoder;

    /** @var  LoggerInterface|null */
    protected $logger;


    /**
     * @param EncoderInterface $encoder
     * @param \Psr\Log\LoggerInterface|null $logger
     */
    public function __construct(EncoderInterface $encoder, LoggerInterface $logger = null)
    {
        $this->encoder = $encoder;
        $this->logger = $logger;
    }

    /**
     * @param \BWC\Component\Jwe\EncoderInterface $encoder
     */
    public function setEncoder($encoder)
    {
        $this->encoder = $encoder;
    }

    /**
     * @return \BWC\Component\Jwe\EncoderInterface
     */
    public function getEncoder()
    {
        return $this->encoder;
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
        if ($context->getResponseJwt()) {
            $keys = $context->optionGet(ContextOptions::KEYS);

            if ($this->logger) {
                $this->logger->debug('EncoderHandler.keys', array('keys'=>$keys));
            }

            if (is_array($keys)) {
                $token = $this->encoder->encode($context->getResponseJwt(), array_shift($keys));

                if ($this->logger) {
                    $this->logger->debug('EncoderHandler.token', array('token'=>$token));
                }

                $context->setResponseToken($token);
            }
        } else if ($this->logger) {
            $this->logger->debug('EncoderHandler.noResponseJwt');
        }
    }

    /**
     * @return string
     */
    public function info()
    {
        return 'EncoderHandler';
    }


} 