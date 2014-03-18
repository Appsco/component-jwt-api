<?php

namespace BWC\Component\JwtApi\Context;

use BWC\Component\JwtApi\Bearer\BearerInterface;
use BWC\Component\Jwe\Jwt;
use Symfony\Component\HttpFoundation\Request;


class JwtContext
{
    /** @var \Symfony\Component\HttpFoundation\Request  */
    protected $request;

    /** @var  string */
    protected $requestBindingType;

    /** @var  string */
    protected $requestJwtToken;

    /** @var  BearerInterface|null */
    protected $bearer;

    /** @var  Jwt */
    protected $requestJwt;

    /** @var  Jwt */
    protected $responseJwt;

    /** @var  null|string */
    protected $destinationUrl;

    /** @var  string */
    protected $responseBindingType = JwtBindingType::CONTENT;

    /** @var  string */
    protected $responseToken;

    /** @var array */
    protected $options = array();



    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param string $requestBindingType
     * @param string $requestJwtToken
     * @param BearerInterface|null $bearer
     * @throws \InvalidArgumentException
     */
    public function __construct(Request $request, $requestBindingType, $requestJwtToken, BearerInterface $bearer = null)
    {
        if (!JwtBindingType::isValid($requestBindingType)) {
            throw new \InvalidArgumentException('Invalid requestBindingType');
        }
        $this->request = $request;
        $this->requestBindingType = $requestBindingType;
        $this->requestJwtToken = $requestJwtToken;
        $this->bearer = $bearer;
    }



    /**
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return \BWC\Component\JwtApi\Bearer\BearerInterface|null
     */
    public function getBearer()
    {
        return $this->bearer;
    }

    /**
     * @return string
     */
    public function getRequestBindingType()
    {
        return $this->requestBindingType;
    }

    /**
     * @return string
     */
    public function getRequestJwtToken()
    {
        return $this->requestJwtToken;
    }


    /**
     * @param \BWC\Component\Jwe\Jwt $requestJwt
     * @return JwtContext|$this
     */
    public function setRequestJwt(Jwt $requestJwt)
    {
        $this->requestJwt = $requestJwt;

        return $this;
    }

    /**
     * @return \BWC\Component\Jwe\Jwt
     */
    public function getRequestJwt()
    {
        return $this->requestJwt;
    }

    /**
     * @param \BWC\Component\Jwe\Jwt $responseJwt
     */
    public function setResponseJwt($responseJwt)
    {
        $this->responseJwt = $responseJwt;
    }

    /**
     * @return \BWC\Component\Jwe\Jwt
     */
    public function getResponseJwt()
    {
        return $this->responseJwt;
    }

    /**
     * @param null|string $destinationUrl
     */
    public function setDestinationUrl($destinationUrl)
    {
        $this->destinationUrl = $destinationUrl;
    }

    /**
     * @return null|string
     */
    public function getDestinationUrl()
    {
        return $this->destinationUrl;
    }

    /**
     * @param string $responseBindingType
     * @throws \InvalidArgumentException
     * @return JwtContext|$this
     */
    public function setResponseBindingType($responseBindingType)
    {
        if (!JwtBindingType::isValid($responseBindingType)) {
            throw new \InvalidArgumentException('Invalid binding type');
        }
        $this->responseBindingType = $responseBindingType;

        return $this;
    }

    /**
     * @return string
     */
    public function getResponseBindingType()
    {
        return $this->responseBindingType;
    }

    /**
     * @param string $responseToken
     */
    public function setResponseToken($responseToken)
    {
        $this->responseToken = $responseToken;
    }

    /**
     * @return string
     */
    public function getResponseToken()
    {
        return $this->responseToken;
    }


    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return JwtContext|$this
     */
    public function optionSet($name, $value)
    {
        $this->options[$name] = $value;

        return $this;
    }

    /**
     * @param string $name
     * @return mixed|null
     */
    public function optionGet($name)
    {
        return @$this->options[$name];
    }


} 