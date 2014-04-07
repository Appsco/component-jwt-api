<?php

namespace BWC\Component\JwtApiBundle\Validator;

use BWC\Component\JwtApiBundle\Context\JwtContext;


class CompositeJwtValidator implements JwtValidatorInterface
{
    /** @var JwtValidatorInterface[] */
    protected $validators = array();



    /**
     * @param \BWC\Component\JwtApiBundle\Context\JwtContext $context
     * @throws \BWC\Component\JwtApiBundle\Error\JwtException
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