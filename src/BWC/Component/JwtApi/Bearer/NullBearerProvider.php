<?php

namespace BWC\Component\JwtApi\Bearer;

use BWC\Component\JwtApi\Context\JwtContext;

class NullBearerProvider implements BearerProviderInterface
{
    /**
     * @param JwtContext $context
     * @return null
     */
    public function getBearer(JwtContext $context)
    {
        return null;
    }

} 