<?php

namespace BWC\Component\JwtApiBundle\Tests\Validator;

use BWC\Component\JwtApiBundle\Validator\CompositeJwtValidator;

class CompositeJwtValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldConstruct()
    {
        new CompositeJwtValidator();
    }

} 