<?php

namespace BWC\Component\JwtApiBundle\Tests\Receiver;

use BWC\Component\JwtApiBundle\Receiver\CompositeReceiver;
use Symfony\Component\HttpFoundation\Request;

class CompositeReceiverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldConstruct()
    {
        new CompositeReceiver();
    }


    /**
     * @test
     */
    public function shouldCallAllReceiversAndStopOnResult()
    {
        $request = new Request();

        $contextMock = $this->getJwtContextMock();

        $composite = new CompositeReceiver();

        $r1 = $this->getReceiverMock();
        $r1->expects($this->once())
            ->method('receive')
            ->with($request)
            ->will($this->returnValue(null));

        $r2 = $this->getReceiverMock();
        $r2->expects($this->once())
            ->method('receive')
            ->with($request)
            ->will($this->returnValue($contextMock));

        $r3 = $this->getReceiverMock();
        $r3->expects($this->never())
            ->method('receive');

        $composite->addReceiver($r1)
            ->addReceiver($r2)
            ->addReceiver($r3);

        $context = $composite->receive($request);

        $this->assertEquals($contextMock, $context);
    }


    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\BWC\Component\JwtApiBundle\Receiver\ReceiverInterface
     */
    protected function getReceiverMock()
    {
        return $this->getMock('BWC\Component\JwtApiBundle\Receiver\ReceiverInterface');
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\BWC\Component\JwtApiBundle\Context\JwtContext
     */
    protected function getJwtContextMock()
    {
        return $this->getMock('BWC\Component\JwtApiBundle\Context\JwtContext', array('none'), array(), '', false);
    }

} 