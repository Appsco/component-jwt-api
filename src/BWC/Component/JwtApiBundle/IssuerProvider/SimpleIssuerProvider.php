<?php

namespace BWC\Component\JwtApiBundle\IssuerProvider;

use BWC\Component\JwtApiBundle\Context\JwtContext;


class SimpleIssuerProvider implements IssuerProviderInterface
{
    /** @var  string */
    protected $issuer;


    /**
     * @param $issuer
     */
    public function __construct($issuer = '')
    {
        $this->issuer = (string)$issuer;
    }



    /**
     * @param JwtContext $context
     * @return string
     */
    public function getIssuer(JwtContext $context)
    {
        return $this->issuer;
    }

    /**
     * @param string $issuer
     * @return SimpleIssuerProvider|$this
     */
    public function setIssuer($issuer)
    {
        $this->issuer = (string)$issuer;

        return $this;
    }





}