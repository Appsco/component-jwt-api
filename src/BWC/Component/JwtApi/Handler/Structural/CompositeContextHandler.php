<?php

namespace BWC\Component\JwtApi\Handler\Structural;

use BWC\Component\JwtApi\Context\ContextOptions;
use BWC\Component\JwtApi\Context\JwtContext;
use BWC\Component\JwtApi\Handler\ContextHandlerInterface;


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

} 