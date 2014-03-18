<?php

namespace BWC\Component\JwtApi;

use Symfony\Component\HttpFoundation\Request;

interface JwtHandlerServiceInterface
{
    /**
     * @param string $type
     * @param HandlerInterface $handler
     * @throws \InvalidArgumentException
     */
    public function addHandler($type, HandlerInterface $handler);

    /**
     * @param Request $request
     * @throws JwtException
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request);

} 