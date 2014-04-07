<?php

namespace BWC\Component\JwtApiBundle\Receiver;

use BWC\Component\JwtApiBundle\Context\JwtContext;
use Symfony\Component\HttpFoundation\Request;

interface ReceiverInterface
{
    /**
     * @param Request $request
     * @return JwtContext|null
     */
    public function receive(Request $request);

} 