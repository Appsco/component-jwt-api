<?php

namespace BWC\Component\JwtApi;

use BWC\Component\Jwe\JwtReceived;

interface JwtValidatorInterface
{
    /**
     * @param JwtReceived $jwt
     * @param array $keys
     * @throws \BWC\Component\Jwe\JweException  When signature invalid
     * @return void
     */
    public function validate(JwtReceived $jwt, array $keys);

} 