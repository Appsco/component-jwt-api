<?php

namespace BWC\Component\JwtApi\Validator;

use BWC\Component\JwtApi\Context\JwtContext;
use BWC\Component\JwtApi\Error\JwtException;
use BWC\Share\Sys\DateTime;


class IssuedTimeValidator implements JwtValidatorInterface
{
    /** @var  int */
    protected $maxIssuedTimeDifference;


    public function __construct($maxIssuedTimeDifference = 120)
    {
        $this->maxIssuedTimeDifference = intval($maxIssuedTimeDifference);
    }


    /**
     * @param \BWC\Component\JwtApi\Context\JwtContext $context
     * @throws \BWC\Component\JwtApi\Error\JwtException
     */
    public function validate(JwtContext $context)
    {
        $delta = abs(DateTime::now() - $context->getRequestJwt()->getIssuedAt());
        if ($delta > $this->maxIssuedTimeDifference) {
            throw new JwtException('Token too old');
        }
    }

} 