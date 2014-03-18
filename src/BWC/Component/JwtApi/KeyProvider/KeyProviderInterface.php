<?php

namespace BWC\Component\JwtApi\KeyProvider;

use BWC\Component\Jwe\Jwt;

interface KeyProviderInterface
{
    /**
     * @param Jwt $jwt
     * @return string[]
     */
    public function getKeys(Jwt $jwt);

} 