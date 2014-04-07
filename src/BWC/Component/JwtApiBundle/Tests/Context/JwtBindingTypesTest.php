<?php

namespace BWC\Component\JwtApiBundle\Tests\Context;

use BWC\Component\JwtApiBundle\Context\JwtBindingTypes;

class JwtBindingTypesTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function shouldIsValidReturnTrueForValidValue()
    {
        $this->assertTrue(JwtBindingTypes::isValid(JwtBindingTypes::CONTENT));
        $this->assertTrue(JwtBindingTypes::isValid(JwtBindingTypes::HTTP_POST));
        $this->assertTrue(JwtBindingTypes::isValid(JwtBindingTypes::HTTP_REDIRECT));
    }

    /**
     * @test
     */
    public function shouldIsValidReturnFalseForInvalidValue()
    {
        $this->assertFalse(JwtBindingTypes::isValid('foo'));
    }

} 