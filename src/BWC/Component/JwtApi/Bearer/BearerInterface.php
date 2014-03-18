<?php

namespace BWC\Component\JwtApi\Bearer;

interface BearerInterface
{
    /**
     * @return string
     */
    public function getSubject();

} 