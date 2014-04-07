<?php

namespace BWC\Component\JwtApiBundle\Validator;

use BWC\Component\Jwe\EncoderInterface;
use BWC\Component\Jwe\Jose;
use BWC\Component\JwtApiBundle\Context\ContextOptions;
use BWC\Component\JwtApiBundle\Context\JwtContext;
use BWC\Component\JwtApiBundle\Error\JwtException;


class SignatureValidator implements JwtValidatorInterface
{
    /** @var  EncoderInterface */
    protected $encoder;

    /**
     * @param EncoderInterface $encoder
     */
    public function __construct(EncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }


    /**
     * @param \BWC\Component\JwtApiBundle\Context\JwtContext $context
     * @throws \Exception
     */
    public function validate(JwtContext $context)
    {
        $jwt = $context->getRequestJwt();
        if (false == $jwt instanceof Jose) {
            throw new JwtException('Expected jose to validate signature');
        }

        $keys = $context->optionGet(ContextOptions::KEYS);
        if (false == is_array($keys)) {
            throw new JwtException('Expected array of keys');
        }

        $exception = null;

        foreach ($keys as $key) {
            try {
                $this->encoder->verify($jwt, $key);

                $exception = null;
                break;

            } catch (\Exception $ex) {
                $exception = $ex;
            }
        }

        if ($exception) {
            throw $exception;
        }
    }

} 