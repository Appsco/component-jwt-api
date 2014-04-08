<?php

namespace BWC\Component\JwtApiBundle\Tests\Handler\Structural;

use BWC\Component\JwtApiBundle\Context\ContextOptions;
use BWC\Component\JwtApiBundle\Context\JwtContext;
use BWC\Component\JwtApiBundle\Handler\Structural\CompositeContextHandler;
use Symfony\Component\PropertyAccess\Exception\RuntimeException;

class CompositeContextHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldConstruct()
    {
        new CompositeContextHandler();
    }

    /**
     * @test
     */
    public function shouldCallEachAddedHandler()
    {
        $contextMock = $this->getJwtContextMock();

        $composite = new CompositeContextHandler();

        $h1 = $this->getContextHandlerMock();
        $h1->expects($this->once())
            ->method('handleContext')
            ->with($contextMock);
        $composite->addContextHandler($h1);

        $h2 = $this->getContextHandlerMock();
        $h2->expects($this->once())
            ->method('handleContext')
            ->with($contextMock);
        $composite->addContextHandler($h2);

        $h3 = $this->getContextHandlerMock();
        $h3->expects($this->once())
            ->method('handleContext')
            ->with($contextMock);
        $composite->addContextHandler($h3);

        $composite->handleContext($contextMock);
    }

    /**
     * @test
     */
    public function shouldStopOnStopOption()
    {
        $contextMock = $this->getJwtContextMock();

        $composite = new CompositeContextHandler();

        $h1 = $this->getContextHandlerMock();
        $h1->expects($this->once())
            ->method('handleContext')
            ->with($contextMock);
        $composite->addContextHandler($h1);

        $h2 = $this->getContextHandlerMock();
        $h2->expects($this->once())
            ->method('handleContext')
            ->with($contextMock)
            ->will($this->returnCallback(function(JwtContext $ctx) {
                $ctx->optionSet(ContextOptions::STOP, true);
            }));
        $composite->addContextHandler($h2);

        $h3 = $this->getContextHandlerMock();
        $h3->expects($this->never())
            ->method('handleContext');
        $composite->addContextHandler($h3);

        $composite->handleContext($contextMock);
    }


    /**
     * @test
     */
    public function shouldThrowOnRaiseExceptionOptionButCallAllHandlers()
    {
        $contextMock = $this->getJwtContextMock();

        $expectedException = new RuntimeException('some message');

        $exceptionStrategyMock = $this->getExceptionStrategyMock();
        $exceptionStrategyMock->expects($this->once())
            ->method('handle')
            ->with($expectedException, $contextMock);

        $composite = new CompositeContextHandler();
        $composite->setExceptionStrategy($exceptionStrategyMock);

        $h1 = $this->getContextHandlerMock();
        $h1->expects($this->once())
            ->method('handleContext')
            ->with($contextMock);
        $composite->addContextHandler($h1);

        $h2 = $this->getContextHandlerMock();
        $h2->expects($this->once())
            ->method('handleContext')
            ->with($contextMock)
            ->will($this->returnCallback(function(JwtContext $ctx) use ($expectedException) {
                $ctx->optionSet(ContextOptions::RAISE_EXCEPTION, $expectedException);
            }));
        $composite->addContextHandler($h2);

        $h3 = $this->getContextHandlerMock();
        $h3->expects($this->once())
            ->method('handleContext')
            ->with($contextMock);
        $composite->addContextHandler($h3);

        $composite->handleContext($contextMock);
    }



    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\BWC\Component\JwtApiBundle\Handler\ContextHandlerInterface
     */
    protected function getContextHandlerMock()
    {
        return $this->getMock('\BWC\Component\JwtApiBundle\Handler\ContextHandlerInterface');
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\BWC\Component\JwtApiBundle\Context\JwtContext
     */
    protected function getJwtContextMock()
    {
        return $this->getMock('BWC\Component\JwtApiBundle\Context\JwtContext', array('none'), array(), '', false);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\BWC\Component\JwtApiBundle\Strategy\Exception\ExceptionStrategyInterface
     */
    protected function getExceptionStrategyMock()
    {
        return $this->getMock('BWC\Component\JwtApiBundle\Strategy\Exception\ExceptionStrategyInterface');
    }
} 