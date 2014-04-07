<?php

namespace BWC\Component\JwtApiBundle\Manager;

use Symfony\Component\HttpFoundation\Request;


interface JwtManagerInterface
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handleRequest(Request $request);

} 