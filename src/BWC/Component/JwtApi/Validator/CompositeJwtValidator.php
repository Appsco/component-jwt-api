<?php

namespace BWC\Component\JwtApi\Validator;

use BWC\Component\JwtApi\Context\JwtContext;


class CompositeJwtValidator implements JwtValidatorInterface
{
    /** @var JwtValidatorInterface[] */
    protected $validators = array();



    /**
     * @param \BWC\Component\JwtApi\Context\JwtContext $context
     * @throws \BWC\Component\JwtApi\Error\JwtException
     */
    public function validate(JwtContext $context)
    {
        foreach ($this->validators as $validator) {
            $validator->validate($context);
        }
    }


    /**
     * @param JwtValidatorInterface $validator
     * @return CompositeJwtValidator|$this
     */
    public function addValidator(JwtValidatorInterface $validator)
    {
        $this->validators[] = $validator;

        return $this;
    }

} 