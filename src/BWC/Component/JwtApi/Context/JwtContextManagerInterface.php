<?php

namespace BWC\Component\JwtApi\Context;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


interface JwtContextManagerInterface
{
    /**
     * @param Request $request
     * @return JwtContext
     */
    public function receive(Request $request);

    /**
     * @param JwtContext $context
     * @return Response
     */
    public function send(JwtContext $context);

} 