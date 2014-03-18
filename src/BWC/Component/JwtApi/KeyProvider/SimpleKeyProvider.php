<?php

namespace BWC\Component\JwtApi\KeyProvider;

use BWC\Component\Jwe\Jwt;

class SimpleKeyProvider implements KeyProviderInterface
{
    /** @var  string[] */
    protected $keys;


    public function __construct(array $keys = array())
    {
        $this->keys = $keys;
    }

    /**
     * @param string $key
     * @return SimpleKeyProvider|$this
     */
    public function addKey($key)
    {
        $this->keys[] = $key;
    }

    /**
     * @param Jwt $jwt
     * @return string[]
     */
    public function getKeys(Jwt $jwt)
    {
        return $this->keys;
    }

} 