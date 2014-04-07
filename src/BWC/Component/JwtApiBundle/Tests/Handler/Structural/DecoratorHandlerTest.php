<?php

namespace BWC\Component\JwtApiBundle\Tests\Handler\Structural;

use BWC\Component\JwtApiBundle\Handler\Structural\DecoratorHandler;

class DecoratorHandlerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function shouldConstruct()
    {
        new DecoratorHandler($this->getContextHandlerMock());
    }

    /**
     * @test
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Must call build() method first to be able to handle context
     */
    public function shouldThrowIfNotBuilt()
    {
        $contextMock = $this->getJwtContextMock();

        $inner = $this->getContextHandlerMock();
        $inner->expects($this->never())
            ->method('handleContext');

        $pre = $this->getContextHandlerMock();
        $pre->expects($this->never())
            ->method('handleContext');

        $post = $this->getContextHandlerMock();
        $post->expects($this->never())
            ->method('handleContext');

        $decorator = new DecoratorHandler($inner);
        $decorator->addPreHandler($pre);
        $decorator->addPostHandler($post);

        $decorator->handleContext($contextMock);
    }


    /**
     * @test
     */
    public function shouldCallInnerIfNoDecoratorsAdded()
    {
        $contextMock = $this->getJwtContextMock();

        $inner = $this->getContextHandlerMock();
        $inner->expects($this->once())
            ->method('handleContext')
            ->with($contextMock);

        $decorator = new DecoratorHandler($inner);
        $decorator->build();

        $decorator->handleContext($contextMock);
    }



    /**
     * @test
     */
    public function shouldCallAllHandlers()
    {
        $contextMock = $this->getJwtContextMock();

        $inner = $this->getContextHandlerMock();
        $inner->expects($this->once())
            ->method('handleContext')
            ->with($contextMock);

        $pre = $this->getContextHandlerMock();
        $pre->expects($this->once())
            ->method('handleContext')
            ->with($contextMock);

        $post = $this->getContextHandlerMock();
        $post->expects($this->once())
            ->method('handleContext')
            ->with($contextMock);

        $decorator = new DecoratorHandler($inner);
        $decorator->addPostHandler($post);
        $decorator->addPreHandler($pre);

        $decorator->build();

        $decorator->handleContext($contextMock);
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

} 