<?php

namespace BWC\Component\JwtApi\Tests\Receiver;


use BWC\Component\JwtApi\Context\JwtBindingTypes;
use BWC\Component\JwtApi\Receiver\JwtReceiver;
use Symfony\Component\HttpFoundation\Request;

class JwtReceiverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldConstruct()
    {
        new JwtReceiver();
    }


    /**
     * @test
     */
    public function shouldReceiveFromRequestGet()
    {
        $request = new Request(array('jwt'=>$token='token'));

        $receiver = new JwtReceiver();

        $context = $receiver->receive($request);

        $this->assertInstanceOf('BWC\Component\JwtApi\Context\JwtContext', $context);
        $this->assertEquals($request, $context->getRequest());
        $this->assertEquals(JwtBindingTypes::HTTP_REDIRECT, $context->getRequestBindingType());
        $this->assertEquals($token, $context->getRequestJwtToken());
    }

    /**
     * @test
     */
    public function shouldReceiveFromRequestPost()
    {
        $request = new Request(array(), array('jwt'=>$token='token'));

        $receiver = new JwtReceiver();

        $context = $receiver->receive($request);

        $this->assertInstanceOf('BWC\Component\JwtApi\Context\JwtContext', $context);
        $this->assertEquals($request, $context->getRequest());
        $this->assertEquals(JwtBindingTypes::HTTP_POST, $context->getRequestBindingType());
        $this->assertEquals($token, $context->getRequestJwtToken());
    }

} 