<?php

namespace BWC\Component\JwtApiBundle\Handler\Functional;

use BWC\Component\JwtApiBundle\Context\JwtContext;
use BWC\Component\JwtApiBundle\Handler\ContextHandlerInterface;
use BWC\Component\JwtApiBundle\Subject\SubjectProviderInterface;
use Psr\Log\LoggerInterface;


class SubjectProviderHandler implements ContextHandlerInterface
{
    /** @var  SubjectProviderInterface */
    protected $subjectProvider;

    /** @var  LoggerInterface|null */
    protected $logger;


    /**
     * @param SubjectProviderInterface $subjectProvider
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(SubjectProviderInterface $subjectProvider, LoggerInterface $logger = null)
    {
        $this->subjectProvider = $subjectProvider;
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
     * @param \BWC\Component\JwtApiBundle\Subject\SubjectProviderInterface $subjectProvider
     */
    public function setSubjectProvider($subjectProvider)
    {
        $this->subjectProvider = $subjectProvider;
    }

    /**
     * @return \BWC\Component\JwtApiBundle\Subject\SubjectProviderInterface
     */
    public function getSubjectProvider()
    {
        return $this->subjectProvider;
    }



    /**
     * @param JwtContext $context
     */
    public function handleContext(JwtContext $context)
    {
        $subject = $this->subjectProvider->getSubject($context);

        if ($this->logger) {
            $this->logger->debug('SubjectProviderHandler', array('subject'=>$subject));
        }

        $context->setSubject($subject);
    }

    /**
     * @return string
     */
    public function info()
    {
        return 'SubjectProviderHandler';
    }


} 