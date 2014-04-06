<?php

namespace BWC\Component\JwtApi\Tests\Context;

use BWC\Component\JwtApi\Context\JwtBindingTypes;
use BWC\Component\JwtApi\Context\JwtContext;
use BWC\Component\JwtApi\Method\MethodJwt;
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


    /**
     * @test
     */
    public function shouldGetRequest()
    {
        $context = new JwtContext($expectedRequest = new Request(), JwtBindingTypes::HTTP_REDIRECT, null);

        $this->assertEquals($expectedRequest, $context->getRequest());
    }

    /**
     * @test
     */
    public function shouldGetRequestBindingType()
    {
        $context = new JwtContext(new Request(), $expectedRequestBindingType = JwtBindingTypes::HTTP_REDIRECT, null);

        $this->assertEquals($expectedRequestBindingType, $context->getRequestBindingType());
    }

    /**
     * @test
     */
    public function shouldGetRequestJwtToken()
    {
        $context = new JwtContext(new Request(), JwtBindingTypes::HTTP_REDIRECT, $expectedToken = 'token');

        $this->assertEquals($expectedToken, $context->getRequestJwtToken());
    }

    /**
     * @test
     */
    public function shouldGetRequestJwt()
    {
        $context = new JwtContext(new Request(), JwtBindingTypes::HTTP_REDIRECT, null);

        $context->setRequestJwt($expectedJwt = new MethodJwt());

        $this->assertEquals($expectedJwt, $context->getRequestJwt());
    }

    /**
     * @test
     */
    public function shouldGetBearer()
    {
        $context = new JwtContext(new Request(), JwtBindingTypes::HTTP_REDIRECT, null);

        $context->setBearer($expectedBearer = 'bearer');

        $this->assertEquals($expectedBearer, $context->getBearer());
    }

    /**
     * @test
     */
    public function shouldGetSubject()
    {
        $context = new JwtContext(new Request(), JwtBindingTypes::HTTP_REDIRECT, null);

        $context->setSubject($expectedSubject = 'subject');

        $this->assertEquals($expectedSubject, $context->getSubject());
    }

}