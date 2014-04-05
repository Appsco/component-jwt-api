<?php

namespace BWC\Component\JwtApi\Test\Context;

use BWC\Component\JwtApi\Context\JwtBindingTypes;
use BWC\Component\JwtApi\Context\JwtContext;
use Symfony\Component\HttpFoundation\Request;

class JwtContextTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldConstruct()
    {
        new JwtContext(new Request(), JwtBindingTypes::HTTP_REDIRECT, null);
    }


    /**
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid request binding type
     */
    public function shouldThrowOnConstructWithInvalidBindingType()
    {
        new JwtContext(new Request(), 'foo', null);
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid binding type
     */
    public function shouldThrowOnSetResponseBindingTypeWithInvalidBindingType()
    {
        $context = new JwtContext(new Request(), JwtBindingTypes::HTTP_REDIRECT, null);
        $context->setResponseBindingType('foo');
    }


    /**
     * @test
     */
    public function shouldReturnSetOption()
    {
        $context = new JwtContext(new Request(), JwtBindingTypes::HTTP_REDIRECT, null);

        $context->optionSet('foo', $expectedFooValue = 'foo.value');
        $context->optionSet('bar', $expectedBarValue = array(1,2,3));

        $this->assertEquals($expectedFooValue, $context->optionGet('foo'));
        $this->assertEquals($expectedBarValue, $context->optionGet('bar'));

        $this->assertEquals(array('foo'=>$expectedFooValue, 'bar'=>$expectedBarValue), $context->getOptions());
    }

} 