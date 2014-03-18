<?php

namespace BWC\Component\JwtApi\Bearer;

use Symfony\Component\HttpFoundation\Request;

interface BearerProviderInterface
{
    /**
     * @param Request $request
     * @return BearerInterface|null
     */
    public function getBearer(Request $request);

} 