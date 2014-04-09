<?php

namespace BWC\Component\JwtApiBundle\Tests\Validator;

use BWC\Component\JwtApiBundle\Validator\IssuedTimeValidator;

class IssuedTimeValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldConstruct()
    {
        new IssuedTimeValidator();
    }
} 