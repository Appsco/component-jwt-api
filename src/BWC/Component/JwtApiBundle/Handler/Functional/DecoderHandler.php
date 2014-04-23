<?php

namespace BWC\Component\JwtApiBundle\Handler\Functional;

use BWC\Component\Jwe\EncoderInterface;
use BWC\Component\JwtApiBundle\Context\JwtContext;
use BWC\Component\JwtApiBundle\Error\JwtException;
use BWC\Component\JwtApiBundle\Handler\ContextHandlerInterface;
use BWC\Component\JwtApiBundle\Method\MethodJwt;
use Psr\Log\LoggerInterface;


class DecoderHandler implements ContextHandlerInterface
{
    /** @var  EncoderInterface */
    protected $encoder;

    /** @var  string */
    protected $class;

    /** @var  LoggerInterface|null */
    protected $logger;


    /**
     * @param EncoderInterface $encoder
     * @param string $class
     * @param \Psr\Log\LoggerInterface|null $logger
     */
    public function __construct(EncoderInterface $encoder,
        $class = null,
        LoggerInterface $logger = null
    ) {
        $this->encoder = $encoder;
        $this->class = $class ?: '\BWC\Component\JwtApiBundle\Method\MethodJwt';
        $this->logger = $logger;
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
     * @param string $class
     */
    public function setClass($class)
    {
        $this->class = $class;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }




    /**
     * @param JwtContext $context
     * @throws \BWC\Component\JwtApiBundle\Error\JwtException
     */
    public function handleContext(JwtContext $context)
    {
        $token = $context->getRequestJwtToken();

        if ($this->logger) {
            $this->logger->debug('DecoderHandler.token', array('token'=>$token));
        }

        /** @var MethodJwt $jwt */
        $jwt = $this->encoder->decode($token, $this->class);

        if ($this->logger) {
            $this->logger->debug('DecoderHandler.jwt', array('jwt'=>$jwt));
        }

        if (false == $jwt instanceof MethodJwt) {
            throw new JwtException(sprintf("Expected MethodJwt but got '%s'", get_class($jwt)));
        }

        $context->setRequestJwt($jwt);
    }

    /**
     * @return string
     */
    public function info()
    {
        return 'DecoderHandler';
    }


} 