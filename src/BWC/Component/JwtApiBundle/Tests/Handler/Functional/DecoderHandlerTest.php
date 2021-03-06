<?php

namespace BWC\Component\JwtApiBundle\Tests\Handler\Functional;

use BWC\Component\JwtApiBundle\Handler\Functional\DecoderHandler;
use BWC\Component\JwtApiBundle\Method\MethodJwt;

class DecoderHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldConstruct()
    {
        new DecoderHandler($this->getEncoderMock());
    }


    /**
     * @test
     */
    public function shouldDecode()
    {
        $class = 'some\class';
        $requestToken = 'token';
        $expectedJwt = new MethodJwt();

        $contextMock = $this->getJwtContextMock();
        $contextMock->expects($this->once())
            ->method('getRequestJwtToken')
            ->will($this->returnValue($requestToken));
        $contextMock->expects($this->once())
            ->method('setRequestJwt')
            ->with($expectedJwt);

        $encoderMock = $this->getEncoderMock();
        $encoderMock->expects($this->once())
            ->method('decode')
            ->with($requestToken, $class)
            ->will($this->returnValue($expectedJwt));

        $handler = new DecoderHandler($encoderMock, $class);

        $handler->handleContext($contextMock);
    }


    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\BWC\Component\Jwe\EncoderInterface
     */
    public function getEncoderMock()
    {
        return $this->getMock('BWC\Component\Jwe\EncoderInterface');
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\BWC\Component\JwtApiBundle\Context\JwtContext
     */
    protected function getJwtContextMock()
    {
        return $this->getMock('BWC\Component\JwtApiBundle\Context\JwtContext', array(), array(), '', false);
    }
} 