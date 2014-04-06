<?php

namespace BWC\Component\JwtApi\Tests\Handler\Functional;

use BWC\Component\JwtApi\Handler\Functional\BearerProviderHandler;

class BearerProviderHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldConstruct()
    {
        new BearerProviderHandler($this->getBearerProviderMock());
    }

    /**
     * @test
     */
    public function shouldSetBearerToContextFromProvider()
    {
        $context = $this->getJwtContextMock();

        $bearerProviderMock = $this->getBearerProviderMock();
        $bearerProviderMock->expects($this->once())
            ->method('getBearer')
            ->with()
            ->will($this->returnValue($expectedBearer = 'bearer'));

        $handler = new BearerProviderHandler($bearerProviderMock);

        $handler->handleContext($context);

        $this->assertEquals($expectedBearer, $context->getBearer());
    }



    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\BWC\Component\JwtApi\Bearer\BearerProviderInterface
     */
    protected function getBearerProviderMock()
    {
        return $this->getMock('BWC\Component\JwtApi\Bearer\BearerProviderInterface');
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\BWC\Component\JwtApi\Context\JwtContext
     */
    protected function getJwtContextMock()
    {
        return $this->getMock('BWC\Component\JwtApi\Context\JwtContext', array('none'), array(), '', false);
    }

} 