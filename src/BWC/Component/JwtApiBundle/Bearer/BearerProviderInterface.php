<?php

namespace BWC\Component\JwtApiBundle\Bearer;

use BWC\Component\JwtApiBundle\Context\JwtContext;

interface BearerProviderInterface
{
    /**
     * @param JwtContext $context
     * @return mixed|null
     */
    public function getBearer(JwtContext $context);

} 