<?php

namespace BWC\Component\JwtApiBundle\Tests\Strategy\Exception;

use BWC\Component\JwtApiBundle\Context\JwtBindingTypes;
use BWC\Component\JwtApiBundle\Strategy\Exception\Logger;
use Symfony\Component\HttpFoundation\Request;

class LoggerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldConstructWithLoggerParam()
    {
        new Logger($this->getLoggerMock());
    }


    /**
     * @test
     */
    public function shouldCallLoggerErrorWhenHandleCalled()
    {
        $exception = new \RuntimeException('foo');

        $loggerMock = $this->getLoggerMock();
        $loggerMock->expects($this->once())
            ->method('error');

        $request = new Request();

        $expectedContext = array('requestBindingType'=>JwtBindingTypes::HTTP_POST, 'requestToken'=>'abc');

        $contextMock = $this->getJwtContextMock();
        $contextMock->expects($this->once())
            ->method('jsonSerialize')
            ->will($this->returnValue($expectedContext));
        $contextMock->expects($this->once())
            ->method('getRequest')
            ->will($this->returnValue($request));

        $logger = new Logger($loggerMock);

        $logger->handle($exception, $contextMock);
    }


    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Psr\Log\LoggerInterface
     */
    protected function getLoggerMock()
    {
        return $this->getMock('Psr\Log\LoggerInterface');
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\BWC\Component\JwtApiBundle\Context\JwtContext
     */
    protected function getJwtContextMock()
    {
        return $this->getMock('BWC\Component\JwtApiBundle\Context\JwtContext', array(), array(), '', false);
    }

} 