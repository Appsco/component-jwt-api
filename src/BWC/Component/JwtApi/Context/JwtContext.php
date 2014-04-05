<?php

namespace BWC\Component\JwtApi\Context;

use BWC\Component\JwtApi\Method\MethodJwt;
use Symfony\Component\HttpFoundation\Request;


class JwtContext implements \JsonSerializable
{
    /** @var \Symfony\Component\HttpFoundation\Request  */
    protected $request;

    /** @var  string */
    protected $requestBindingType;

    /** @var  string */
    protected $requestJwtToken;

    /** @var  mixed|null */
    protected $bearer;

    /** @var  MethodJwt */
    protected $requestJwt;

    /** @var  mixed|null */
    protected $subject;

    /** @var  \BWC\Component\JwtApi\Method\MethodJwt */
    protected $responseJwt;

    /** @var  null|string */
    protected $destinationUrl;

    /** @var  string */
    protected $responseBindingType;

    /** @var  string */
    protected $responseToken;

    /** @var array */
    protected $options = array();



    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param string $requestBindingType
     * @param string $requestJwtToken
     * @throws \InvalidArgumentException
     */
    public function __construct(Request $request, $requestBindingType, $requestJwtToken)
    {
        if (!JwtBindingTypes::isValid($requestBindingType)) {
            throw new \InvalidArgumentException('Invalid request binding type');
        }
        $this->request = $request;
        $this->requestBindingType = $requestBindingType;
        $this->requestJwtToken = $requestJwtToken;
    }



    /**
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest()
    {
        return $this->request;
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
     * @param \BWC\Component\JwtApi\Method\MethodJwt $requestJwt
     * @return JwtContext|$this
     */
    public function setRequestJwt(MethodJwt $requestJwt)
    {
        $this->requestJwt = $requestJwt;

        return $this;
    }

    /**
     * @return \BWC\Component\JwtApi\Method\MethodJwt
     */
    public function getRequestJwt()
    {
        return $this->requestJwt;
    }

    /**
     * @return mixed|null
     */
    public function getBearer()
    {
        return $this->bearer;
    }

    /**
     * @param mixed|null $bearer
     * @return JwtContext|$this
     */
    public function setBearer($bearer)
    {
        $this->bearer = $bearer;

        return $this;
    }

    /**
     * @param mixed|null $subject
     * @return JwtContext|$this
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * @return mixed|null
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param \BWC\Component\JwtApi\Method\MethodJwt $responseJwt
     */
    public function setResponseJwt(MethodJwt $responseJwt)
    {
        $this->responseJwt = $responseJwt;
    }

    /**
     * @return \BWC\Component\JwtApi\Method\MethodJwt
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
        if (!JwtBindingTypes::isValid($responseBindingType)) {
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


    /**
     * (PHP 5 &gt;= 5.4.0)<br/>
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     */
    public function jsonSerialize()
    {
        return array(
            'requestBindingType' => $this->requestBindingType,
            'requestToken' => $this->requestJwtToken,
            'requestJwt' => $this->requestJwt,
            'responseJwt' => $this->responseJwt,
            'destinationUrl' => $this->destinationUrl,
            'responseBindingType' => $this->responseBindingType,
            'responseToken' => $this->responseToken,
        );
    }


}