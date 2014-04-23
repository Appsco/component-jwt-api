<?php

namespace BWC\Component\JwtApiBundle\Handler\Functional;

use BWC\Component\JwtApiBundle\Context\JwtContext;
use BWC\Component\JwtApiBundle\Handler\ContextHandlerInterface;
use BWC\Component\JwtApiBundle\Validator\JwtValidatorInterface;
use Psr\Log\LoggerInterface;


class ValidatorHandler implements ContextHandlerInterface
{
    /** @var  JwtValidatorInterface */
    protected $validator;

    /** @var  LoggerInterface|null */
    protected $logger;


    /**
     * @param JwtValidatorInterface $validator
     * @param LoggerInterface $logger
     */
    public function __construct(JwtValidatorInterface $validator, LoggerInterface $logger = null)
    {
        $this->validator = $validator;
        $this->logger = $logger;
    }


    /**
     * @param JwtContext $context
     * @throws \BWC\Component\JwtApiBundle\Error\JwtException
     */
    public function handleContext(JwtContext $context)
    {
        if ($this->logger) {
            $this->logger->debug('ValidatorHandler', array('context'=>$context));
        }

        $this->validator->validate($context);
    }

    /**
     * @return string
     */
    public function info()
    {
        return 'ValidatorHandler';
    }


} 