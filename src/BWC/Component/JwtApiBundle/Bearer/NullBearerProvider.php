<?php

namespace BWC\Component\JwtApiBundle\Bearer;

use BWC\Component\JwtApiBundle\Context\JwtContext;

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