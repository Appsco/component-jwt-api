<?php

namespace BWC\Component\JwtApi;

use BWC\Component\Jwe\Encoder;
use BWC\Component\Jwe\JweException;
use BWC\Component\Jwe\JwtReceived;
use BWC\Share\Sys\DateTime;

class JwtValidator implements JwtValidatorInterface
{
    /** @var  Encoder */
    protected $jwtEncoder;

    /** @var  int */
    protected $maxIssuedTimeDifference;


    /**
     * @param Encoder $jwtEncoder
     * @param int $maxIssuedTimeDifference
     */
    public function __construct(Encoder $jwtEncoder, $maxIssuedTimeDifference = 120)
    {
        $this->jwtEncoder = $jwtEncoder;
        $this->maxIssuedTimeDifference = intval($maxIssuedTimeDifference);
    }


    /**
     * @param JwtReceived $jwt
     * @param array $keys
     * @throws JwtException  When issuer not specified or unknown
     * @throws \BWC\Component\Jwe\JweException  When signature invalid
     * @return void
     */
    public function validate(JwtReceived $jwt, array $keys)
    {
        $this->verifyIssuedTime($jwt);
        $this->verifySignature($jwt, $keys);
    }

    /**
     * @param JwtReceived $jwt
     * @throws JwtException
     */
    protected function verifyIssuedTime(JwtReceived $jwt)
    {
        $delta = abs(DateTime::now() - $jwt->getIssuedAt());
        if ($delta > $this->maxIssuedTimeDifference) {
            throw new JwtException('Token too old');
        }
    }


    /**
     * @param JwtReceived $jwt
     * @param array $keys
     * @throws JwtException  When issuer not specified or unknown
     * @throws \BWC\Component\Jwe\JweException  When signature invalid
     */
    public function verifySignature(JwtReceived $jwt, array $keys)
    {
        $firstException = null;
        foreach ($keys as $key) {
            try {
                $this->jwtEncoder->verify($jwt, $key);
                $firstException = null;
            } catch (JweException $ex) {
                if ($firstException == null) {
                    $firstException = $ex;
                }
            }
        }

        if ($firstException) {
            throw $firstException;
        }
    }

} 