<?php

namespace BWC\Component\JwtApiBundle\Handler\Functional;

use BWC\Component\JwtApiBundle\Context\JwtContext;
use BWC\Component\JwtApiBundle\Handler\ContextHandlerInterface;
use BWC\Component\JwtApiBundle\Subject\SubjectProviderInterface;


class SubjectProviderHandler implements ContextHandlerInterface
{
    /** @var  SubjectProviderInterface */
    protected $subjectProvider;


    /**
     * @param SubjectProviderInterface $subjectProvider
     */
    public function __construct(SubjectProviderInterface $subjectProvider)
    {
        $this->subjectProvider = $subjectProvider;
    }


    /**
     * @param JwtContext $context
     */
    public function handleContext(JwtContext $context)
    {
        $context->setSubject($this->subjectProvider->getSubject($context));
    }

    /**
     * @return string
     */
    public function info()
    {
        return 'SubjectProviderHandler';
    }


} 