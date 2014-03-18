<?php

namespace BWC\Component\JwtApi\Method;

use BWC\Component\Jwe\JwtClaim;
use BWC\Component\Jwe\Jwt;
use BWC\Share\Sys\DateTime;

class MethodJwt extends Jwt
{
    const PAYLOAD_TYPE = 'appsco/method';


    /**
     * @param string $issuer
     * @param string|null $method
     * @param mixed $data
     * @param string|null $inResponseTo
     * @return MethodJwt
     */
    static public function create($issuer, $method = null, $data = null, $inResponseTo = null)
    {
        $payload = array(
                JwtClaim::ISSUER => $issuer,
                JwtClaim::ISSUED_AT => DateTime::now(),
                JwtClaim::TYPE => self::PAYLOAD_TYPE
        );
        if ($method) {
            $payload[MethodClaim::METHOD] = $method;
        }
        if ($data) {
            $payload[MethodClaim::DATA] = $data;
        }
        if ($inResponseTo) {
            $payload[MethodClaim::IN_RESPONSE_TO] = $inResponseTo;
        }

        $result = new MethodJwt(array(), $payload);

        return $result;
    }



    public function __construct(array $header = array(), array $payload = array())
    {
        parent::__construct(array(), $payload);
    }

    /**
     * @param $command
     * @return $this|MethodJwt
     */
    public function setMethod($command)
    {
        return $this->set(MethodClaim::METHOD, $command);
    }

    /**
     * @return string|null
     */
    public function getMethod()
    {
        return $this->get(MethodClaim::METHOD);
    }


    /**
     * @param mixed $data
     * @return $this|MethodJwt
     */
    public function setData($data)
    {
        return $this->set(MethodClaim::DATA, $data);
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->get(MethodClaim::DATA);
    }


    /**
     * @param string $inResponseTo
     * @return $this|MethodJwt
     */
    public function setInResponseTo($inResponseTo)
    {
        return $this->set(MethodClaim::IN_RESPONSE_TO, $inResponseTo);
    }

    /**
     * @return string
     */
    public function getInResponseTo()
    {
        return $this->get(MethodClaim::IN_RESPONSE_TO);
    }
}