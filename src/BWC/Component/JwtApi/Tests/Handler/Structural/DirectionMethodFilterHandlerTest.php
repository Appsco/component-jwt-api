<?php

namespace BWC\Component\JwtApi\Tests\Handler\Structural;

use BWC\Component\JwtApi\Handler\Structural\DirectionMethodFilterHandler;
use BWC\Component\JwtApi\Method\Directions;
use BWC\Component\JwtApi\Method\MethodClaims;

class DirectionMethodFilterHandlerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function shouldConstruct()
    {
        new DirectionMethodFilterHandler($this->getContextHandlerMock(), 'direction', 'method');
    }

    /**
     * @test
     */
    public function shouldCallInnerIfMatch()
    {
        $contextMock = $this->getJwtContextMock();

        $inner = $this->getContextHandlerMock();
        $inner->expects($this->once())
            ->method('handleContext')
            ->with($contextMock);

        $expectedDirection = Directions::REQUEST;
        $expectedMethod = 'method-one';

        $filter = new DirectionMethodFilterHandler($inner, $expectedDirection, $expectedMethod);

        $requestJwtMock = $this->getMethodJwtMock(
            array(),
            array(
                MethodClaims::DIRECTION => $expectedDirection,
                MethodClaims::METHOD => $expectedMethod
            )
        );

        $contextMock->setRequestJwt($requestJwtMock);

        $filter->handleContext($contextMock);
    }

    /**
     * @test
     */
    public function shouldNotCallInnerIfMethodDoesNotMatch()
    {
        $contextMock = $this->getJwtContextMock();

        $inner = $this->getContextHandlerMock();
        $inner->expects($this->never())
            ->method('handleContext');

        $expectedDirection = Directions::REQUEST;
        $expectedMethod = 'method-one';

        $filter = new DirectionMethodFilterHandler($inner, $expectedDirection, $expectedMethod);

        $requestJwtMock = $this->getMethodJwtMock(
            array(),
            array(
                MethodClaims::DIRECTION => $expectedDirection,
                MethodClaims::METHOD => 'method-two'
            )
        );

        $contextMock->setRequestJwt($requestJwtMock);

        $filter->handleContext($contextMock);
    }


    /**
     * @test
     */
    public function shouldNotCallInnerIfDirectionDoesNotMatch()
    {
        $contextMock = $this->getJwtContextMock();

        $inner = $this->getContextHandlerMock();
        $inner->expects($this->never())
            ->method('handleContext');

        $expectedDirection = Directions::REQUEST;
        $expectedMethod = 'method-one';

        $filter = new DirectionMethodFilterHandler($inner, $expectedDirection, $expectedMethod);

        $requestJwtMock = $this->getMethodJwtMock(
            array(),
            array(
                MethodClaims::DIRECTION => Directions::RESPONSE,
                MethodClaims::METHOD => $expectedMethod
            )
        );

        $contextMock->setRequestJwt($requestJwtMock);

        $filter->handleContext($contextMock);
    }

    /**
     * @test
     */
    public function shouldNotCallInnerIfDirectionAndMethodDoNotMatch()
    {
        $contextMock = $this->getJwtContextMock();

        $inner = $this->getContextHandlerMock();
        $inner->expects($this->never())
            ->method('handleContext');

        $expectedDirection = Directions::REQUEST;
        $expectedMethod = 'method-one';

        $filter = new DirectionMethodFilterHandler($inner, $expectedDirection, $expectedMethod);

        $requestJwtMock = $this->getMethodJwtMock(
            array(),
            array(
                MethodClaims::DIRECTION => Directions::RESPONSE,
                MethodClaims::METHOD => 'method-two'
            )
        );

        $contextMock->setRequestJwt($requestJwtMock);

        $filter->handleContext($contextMock);
    }


    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\BWC\Component\JwtApi\Handler\ContextHandlerInterface
     */
    protected function getContextHandlerMock()
    {
        return $this->getMock('\BWC\Component\JwtApi\Handler\ContextHandlerInterface');
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\BWC\Component\JwtApi\Context\JwtContext
     */
    protected function getJwtContextMock()
    {
        return $this->getMock('BWC\Component\JwtApi\Context\JwtContext', array('none'), array(), '', false);
    }

    /**
     * @param array $ctorArguments
     * @return \PHPUnit_Framework_MockObject_MockObject|\BWC\Component\JwtApi\Method\MethodJwt
     */
    protected function getMethodJwtMock(array $header, array $payload)
    {
        return $this->getMock('BWC\Component\JwtApi\Method\MethodJwt', array('none'), array($header, $payload));
    }
} 