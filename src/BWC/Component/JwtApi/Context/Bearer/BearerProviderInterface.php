<?php

namespace BWC\Component\JwtApi\Context\Bearer;

use Symfony\Component\HttpFoundation\Request;

interface BearerProviderInterface
{
    /**
     * @param Request $request
     * @return mixed|null
     */
    public function getBearer(Request $request);

} 