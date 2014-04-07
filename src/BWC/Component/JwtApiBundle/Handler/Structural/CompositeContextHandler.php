<?php

namespace BWC\Component\JwtApiBundle\Handler\Structural;

use BWC\Component\JwtApiBundle\Context\ContextOptions;
use BWC\Component\JwtApiBundle\Context\JwtContext;
use BWC\Component\JwtApiBundle\Handler\ContextHandlerInterface;


class CompositeContextHandler implements ContextHandlerInterface
{
    /** @var ContextHandlerInterface[] */
    protected $contextHandlers = array();


    /**
     * @param JwtContext $context
     * @throws \Exception
     */
    public function handleContext(JwtContext $context)
    {
        foreach ($this->contextHandlers as $handle) {
            $handle->handleContext($context);

            if ($context->optionGet(ContextOptions::STOP)) {
                break;
            }
        }

        if ($ex = $context->optionGet(ContextOptions::RAISE_EXCEPTION)) {
            if (false == $ex instanceof \Exception) {
                throw new \RuntimeException('Expected Exception');
            }
            throw $ex;
        }

    }


    /**
     * @param ContextHandlerInterface $handler
     * @return CompositeContextHandler|$this
     */
    public function addContextHandler(ContextHandlerInterface $handler)
    {
        $this->contextHandlers[] = $handler;

        return $this;
    }

    /**
     * @return \BWC\Component\JwtApiBundle\Handler\ContextHandlerInterface[]
     */
    public function getContextHandlers()
    {
        return $this->contextHandlers;
    }



} 