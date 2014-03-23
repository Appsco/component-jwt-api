<?php

namespace BWC\Component\JwtApi\Context\Bearer;

use Symfony\Component\HttpFoundation\Request;

class NullBearerProvider implements BearerProviderInterface
{
    /**
     * @param Request $request
     * @return null
     */
    public function getBearer(Request $request)
    {
        return null;
    }

} 