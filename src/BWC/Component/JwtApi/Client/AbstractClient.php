<?php

namespace BWC\Component\JwtApi\Client;

use Appsco\My\Api\Model\Order\OrderRequest;
use BWC\Component\Jwe\Algorithm;
use BWC\Component\Jwe\EncoderInterface;
use BWC\Component\JwtApi\Context\JwtBindingType;
use BWC\Component\JwtApi\Method\MethodJwt;

abstract class AbstractClient
{
    /** @var string */
    protected $defaultBinding = JwtBindingType::HTTP_POST;

    /** @var string */
    protected $algorithm = Algorithm::HS512;

    /** @var  string */
    protected $audience;

    /** @var  string */
    protected $issuer;

    /** @var  string */
    protected $targetUrl;

    /** @var  string */
    protected $key;

    /** @var EncoderInterface  */
    protected $encoder;



    /**
     * @param string $issuer
     * @param string $targetUrl
     * @param string $key
     * @param EncoderInterface $encoder
     */
    public function __construct($issuer, $targetUrl, $key, EncoderInterface $encoder)
    {
        $this->issuer = (string)$issuer;
        $this->targetUrl = (string)$targetUrl;
        $this->key = (string)$key;
        $this->encoder = $encoder;
    }





    /**
     * @param string $binding
     * @throws \InvalidArgumentException
     * @return DetachedClient|$this
     */
    public function setDefaultBinding($binding)
    {
        if (!JwtBindingType::isValid($binding)) {
            throw new \InvalidArgumentException('Invalid binding type '.$binding);
        }
        $this->defaultBinding = $binding;

        return $this;
    }

    /**
     * @return string
     */
    public function getDefaultBinding()
    {
        return $this->defaultBinding;
    }

    /**
     * @param string $algorithm
     * @throws \InvalidArgumentException
     * @return DetachedClient|$this
     */
    public function setAlgorithm($algorithm)
    {
        if (!Algorithm::isValid($algorithm)) {
            throw new \InvalidArgumentException('Invalid algorithm '.$algorithm);
        }
        $this->algorithm = $algorithm;

        return $this;
    }

    /**
     * @return string
     */
    public function getAlgorithm()
    {
        return $this->algorithm;
    }

    /**
     * @param string $audience
     */
    public function setAudience($audience)
    {
        $this->audience = $audience;
    }

    /**
     * @return string
     */
    public function getAudience()
    {
        return $this->audience;
    }



    // ------------------------------------------------------


    /**
     * @return string
     */
    protected function getRedirectUrl()
    {
        $url = $this->targetUrl;
        if (($pos = strpos($url, '?')) !== false) {
            $url = substr($url, 0, $pos);
        }

        return $url;
    }

    /**
     * @param string|null $binding
     * @throws \InvalidArgumentException
     */
    protected function checkBinding(&$binding)
    {
        $binding = $binding ? $binding : $this->getDefaultBinding();
        if (!JwtBindingType::isValid($binding)) {
            throw new \InvalidArgumentException('Invalid binding type '.$binding);
        }
    }

} 