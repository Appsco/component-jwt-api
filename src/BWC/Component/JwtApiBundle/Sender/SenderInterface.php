<?php

namespace BWC\Component\JwtApiBundle\Sender;

use BWC\Component\JwtApiBundle\Context\JwtContext;
use Symfony\Component\HttpFoundation\Response;

interface SenderInterface
{
    /**
     * @param JwtContext $context
     * @return Response
     */
    public function send(JwtContext $context);

} 