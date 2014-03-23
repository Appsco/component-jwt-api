<?php

namespace BWC\Component\JwtApi\Method\Event;

use BWC\Component\JwtApi\Context\JwtContext;
use BWC\Component\JwtApi\Method\MethodInterface;
use Symfony\Component\EventDispatcher\Event;


class MethodEvent extends Event
{
    /** @var JwtContext  */
    protected $context;

    /** @var  MethodInterface */
    protected $method;

    /** @var  bool */
    protected $handled = false;



    /**
     * @param JwtContext $context
     * @param \BWC\Component\JwtApi\Method\MethodInterface $method
     */
    public function __construct(JwtContext $context, MethodInterface $method)
    {
        $this->context = $context;
        $this->method = $method;
    }



    /**
     * @return JwtContext
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @return MethodInterface
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param boolean $handled
     */
    public function setHandled($handled)
    {
        $this->handled = (bool)$handled;
    }

    /**
     * @return boolean
     */
    public function isHandled()
    {
        return $this->handled;
    }



} 