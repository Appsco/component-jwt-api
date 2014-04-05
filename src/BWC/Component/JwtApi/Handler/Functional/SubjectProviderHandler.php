<?php

namespace BWC\Component\JwtApi\Handler\Functional;

use BWC\Component\JwtApi\Context\JwtContext;
use BWC\Component\JwtApi\Handler\ContextHandlerInterface;
use BWC\Component\JwtApi\Subject\SubjectProviderInterface;


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

} 