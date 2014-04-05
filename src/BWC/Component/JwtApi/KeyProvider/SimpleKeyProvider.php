<?php

namespace BWC\Component\JwtApi\KeyProvider;

use BWC\Component\JwtApi\Context\JwtContext;


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
     * @param JwtContext $context
     * @return string[]
     */
    public function getKeys(JwtContext $context)
    {
        return $this->keys;
    }

} 