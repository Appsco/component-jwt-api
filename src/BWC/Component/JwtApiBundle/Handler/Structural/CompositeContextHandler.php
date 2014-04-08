<?php

namespace BWC\Component\JwtApiBundle\Handler\Structural;

use BWC\Component\JwtApiBundle\Context\ContextOptions;
use BWC\Component\JwtApiBundle\Context\JwtContext;
use BWC\Component\JwtApiBundle\Handler\ContextHandlerInterface;
use BWC\Component\JwtApiBundle\Strategy\Exception\ExceptionStrategyInterface;


class CompositeContextHandler implements ContextHandlerInterface
{
    /** @var ContextHandlerInterface[] */
    protected $contextHandlers = array();

    /** @var  ExceptionStrategyInterface|null */
    protected $exceptionStrategy;



    /**
     * @param JwtContext $context
     * @throws \Exception
     */
    public function handleContext(JwtContext $context)
    {
        try {

            $this->iterateChildren($context);

        } catch (\Exception $ex) {
            if ($this->exceptionStrategy) {
                $this->exceptionStrategy->handle($ex, $context);
            }
        }
    }


    /**
     * @param \BWC\Component\JwtApiBundle\Strategy\Exception\ExceptionStrategyInterface|null $exceptionStrategy
     */
    public function setExceptionStrategy($exceptionStrategy)
    {
        $this->exceptionStrategy = $exceptionStrategy;
    }

    /**
     * @return \BWC\Component\JwtApiBundle\Strategy\Exception\ExceptionStrategyInterface|null
     */
    public function getExceptionStrategy()
    {
        return $this->exceptionStrategy;
    }



    /**
     * @param JwtContext $context
     * @throws \Exception
     */
    protected function iterateChildren(JwtContext $context)
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