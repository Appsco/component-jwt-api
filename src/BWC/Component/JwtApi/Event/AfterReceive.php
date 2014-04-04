<?php

namespace BWC\Component\JwtApi\Event;

use BWC\Component\JwtApi\Context\JwtContext;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;

class AfterReceive extends Event
{
    /** @var  Request */
    protected $request;

    /** @var  JwtContext */
    protected $context;


    /**
     * @param Request $request
     * @param JwtContext $context
     */
    public function __construct(Request $request, JwtContext $context)
    {
        $this->request = $request;
        $this->context = $context;
    }

    /**
     * @return \BWC\Component\JwtApi\Context\JwtContext
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest()
    {
        return $this->request;
    }




}