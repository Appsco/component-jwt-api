<?php

namespace BWC\Component\JwtApi\Bearer;

use BWC\Component\JwtApi\Context\JwtContext;

interface BearerProviderInterface
{
    /**
     * @param JwtContext $context
     * @return mixed|null
     */
    public function getBearer(JwtContext $context);

} 