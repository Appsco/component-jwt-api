<?php

namespace BWC\Component\JwtApi;

use BWC\Component\Jwe\Encoder;
use BWC\Component\Jwe\JweException;
use BWC\Component\Jwe\JwtReceived;
use BWC\Share\Sys\DateTime;

class JwtValidator implements JwtValidatorInterface
{
    const MAX_ISSUED_TIME_DIFFERENCE = 120;


    /** @var  Encoder */
    protected $jwtEncoder;



    public function __construct(Encoder $jwtEncoder)
    {
        $this->jwtEncoder = $jwtEncoder;
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
        if ($delta > self::MAX_ISSUED_TIME_DIFFERENCE) {
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