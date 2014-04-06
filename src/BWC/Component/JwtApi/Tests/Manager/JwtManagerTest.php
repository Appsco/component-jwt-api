<?php

namespace BWC\Component\JwtApi\Tests\Manager;

use BWC\Component\JwtApi\Manager\JwtManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class JwtManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldConstruct()
    {
        new JwtManager($this->getReceiverMock(), $this->getSenderMock());
    }

    /**
     * @test
     */
    public function shouldReceiveContextFromRequestThenHandleContextThenSendContextWithSender()
    {
        $request = new Request();

        $contextMock = $this->getJwtContextMock();

        $receiverMock = $this->getReceiverMock();
        $receiverMock->expects($this->once())
            ->method('receive')
            ->with($request)
            ->will($this->returnValue($contextMock));

        $handlerMock = $this->getContextHandlerMock();
        $handlerMock->expects($this->once())
            ->method('handleContext')
            ->with($contextMock);

        $expectedResponse = new Response('response');

        $senderMock = $this->getSenderMock();
        $senderMock->expects($this->once())
            ->method('send')
            ->with($contextMock)
            ->will($this->returnValue($expectedResponse));

        $manager = new JwtManager($receiverMock, $senderMock);

        $manager->addContextHandler($handlerMock);

        $response = $manager->handleRequest($request);

        $this->assertEquals($expectedResponse, $response);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\BWC\Component\JwtApi\Receiver\ReceiverInterface
     */
    protected function getReceiverMock()
    {
        return $this->getMock('BWC\Component\JwtApi\Receiver\ReceiverInterface');
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\BWC\Component\JwtApi\Sender\SenderInterface
     */
    protected function getSenderMock()
    {
        return $this->getMock('BWC\Component\JwtApi\Sender\SenderInterface');
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\BWC\Component\JwtApi\Handler\ContextHandlerInterface
     */
    protected function getContextHandlerMock()
    {
        return $this->getMock('BWC\Component\JwtApi\Handler\ContextHandlerInterface');
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\BWC\Component\JwtApi\Context\JwtContext
     */
    protected function getJwtContextMock()
    {
        return $this->getMock('BWC\Component\JwtApi\Context\JwtContext', array('none'), array(), '', false);
    }
} 