<?php

namespace BWC\Component\JwtApi\Method;

use BWC\Component\Jwe\JwtClaim;
use BWC\Component\Jwe\Jwt;
use BWC\Share\Sys\DateTime;


class MethodJwt extends Jwt
{
    const PAYLOAD_TYPE = 'bwc-method';


    /**
     * @param Jwt $jwt
     * @return MethodJwt
     */
    public static function createFromJwt(Jwt $jwt)
    {
        return new MethodJwt($jwt->getHeader(), $jwt->getPayload());
    }

    /**
     * @param string $direction
     * @param string $issuer
     * @param string $method
     * @param string|null $instance
     * @param mixed $data
     * @param string|null $inResponseTo
     * @throws \InvalidArgumentException
     * @return MethodJwt
     */
    public static function create($direction, $issuer, $method, $instance = null, $data = null, $inResponseTo = null)
    {
        if (!Directions::isValid($direction)) {
            throw new \InvalidArgumentException(sprintf("Invalid direction value '%s'", $direction));
        }
        $payload = array(
                JwtClaim::ISSUER => $issuer,
                JwtClaim::ISSUED_AT => DateTime::now(),
                JwtClaim::TYPE => self::PAYLOAD_TYPE,
                MethodClaims::DIRECTION => $direction,
                MethodClaims::METHOD => $method,
        );
        if ($instance) {
            $payload[MethodClaims::INSTANCE] = $instance;
        }
        if ($data) {
            $payload[MethodClaims::DATA] = $data;
        }
        if ($inResponseTo) {
            $payload[MethodClaims::IN_RESPONSE_TO] = $inResponseTo;
        }

        $result = new MethodJwt(array(), $payload);

        return $result;
    }



    public function __construct(array $header = array(), array $payload = array())
    {
        parent::__construct($header, $payload);
    }



    /**
     * @param string $instance
     * @return $this|MethodJwt
     */
    public function setInstance($instance)
    {
        return $this->set(MethodClaims::INSTANCE, $instance);
    }

    /**
     * @return string|null
     */
    public function getInstance()
    {
        return $this->get(MethodClaims::INSTANCE);
    }

    /**
     * @param string $method
     * @return $this|MethodJwt
     */
    public function setMethod($method)
    {
        return $this->set(MethodClaims::METHOD, $method);
    }

    /**
     * @return string|null
     */
    public function getMethod()
    {
        return $this->get(MethodClaims::METHOD);
    }


    /**
     * @param mixed $data
     * @return $this|MethodJwt
     */
    public function setData($data)
    {
        return $this->set(MethodClaims::DATA, $data);
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->get(MethodClaims::DATA);
    }


    /**
     * @param string $inResponseTo
     * @return $this|MethodJwt
     */
    public function setInResponseTo($inResponseTo)
    {
        return $this->set(MethodClaims::IN_RESPONSE_TO, $inResponseTo);
    }

    /**
     * @return string
     */
    public function getInResponseTo()
    {
        return $this->get(MethodClaims::IN_RESPONSE_TO);
    }

    /**
     * @param string $replyTo
     * @return $this|MethodJwt
     */
    public function setReplyTo($replyTo)
    {
        return $this->set(MethodClaims::REPLY_TO, $replyTo);
    }

    /**
     * @return string
     */
    public function getReplyTo()
    {
        return $this->get(MethodClaims::REPLY_TO);
    }

    /**
     * @param string $exception
     * @return $this|MethodJwt
     */
    public function setException($exception)
    {
        return $this->set(MethodClaims::EXCEPTION, $exception);
    }

    /**
     * @return string
     */
    public function getException()
    {
        return $this->get(MethodClaims::EXCEPTION);
    }

}