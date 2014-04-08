<?php

namespace BWC\Component\JwtApiBundle\Handler\Structural;

use BWC\Component\JwtApiBundle\Context\JwtContext;
use BWC\Component\JwtApiBundle\Handler\ContextHandlerInterface;

class DecoratorHandler extends CompositeContextHandler
{
    /** @var bool  */
    protected $isBuilt = false;

    /** @var ContextHandlerInterface[] */
    protected $preHandlers = array();

    /** @var ContextHandlerInterface */
    protected $innerHandler;

    /** @var ContextHandlerInterface[] */
    protected $postHandlers = array();



    /**
     * @param ContextHandlerInterface $innerHandler
     */
    public function __construct(ContextHandlerInterface $innerHandler)
    {
        $this->innerHandler = $innerHandler;
    }


    /**
     * @param JwtContext $context
     * @throws \Exception
     */
    public function handleContext(JwtContext $context)
    {
        if (false == $this->isBuilt) {
            throw new \RuntimeException('Must call build() method first to be able to handle context');
        }

        parent::handleContext($context);
    }

    /**
     * @param ContextHandlerInterface $handler
     * @throws \LogicException
     * @return DecoratorHandler|$this
     */
    public function addPreHandler(ContextHandlerInterface $handler)
    {
        if ($this->isBuilt) {
            throw new \LogicException('Can not add pre handlers once built');
        }

        $this->preHandlers[] = $handler;

        return $this;
    }

    /**
     * @param ContextHandlerInterface $handler
     * @throws \LogicException
     * @return DecoratorHandler|$this
     */
    public function addPostHandler(ContextHandlerInterface $handler)
    {
        if ($this->isBuilt) {
            throw new \LogicException('Can not add post handlers once built');
        }

        $this->postHandlers[] = $handler;

        return $this;
    }


    /**
     * @return DecoratorHandler|$this
     */
    public function build()
    {
        $this->contextHandlers = array();

        foreach ($this->preHandlers as $handler) {
            $this->contextHandlers[] = $handler;
        }

        $this->contextHandlers[] = $this->innerHandler;

        foreach ($this->postHandlers as $handler) {
            $this->contextHandlers[] = $handler;
        }

        $this->isBuilt = true;

        return $this;
    }


    /**
     * @return string
     */
    public function info()
    {
        return 'DecoratorHandler';
    }


}