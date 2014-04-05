<?php

namespace BWC\Component\JwtApi\Sender;

use BWC\Component\JwtApi\Context\JwtContext;
use Symfony\Component\HttpFoundation\Response;

interface SenderInterface
{
    /**
     * @param JwtContext $context
     * @return Response
     */
    public function send(JwtContext $context);

} 