<?php

namespace BWC\Component\JwtApi\Bearer;

use Symfony\Component\HttpFoundation\Request;

class NullBearerProvider implements BearerProviderInterface
{
    /**
     * @param Request $request
     * @return BearerInterface|null
     */
    public function getBearer(Request $request)
    {
        return null;
    }

} 