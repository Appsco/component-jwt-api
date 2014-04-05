<?php

namespace BWC\Component\JwtApi\Receiver;

use BWC\Component\JwtApi\Context\JwtContext;
use Symfony\Component\HttpFoundation\Request;

interface ReceiverInterface
{
    /**
     * @param Request $request
     * @return JwtContext|null
     */
    public function receive(Request $request);

} 