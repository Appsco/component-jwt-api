<?php

namespace BWC\Component\JwtApi\Tests\Handler\Functional;

use BWC\Component\JwtApi\Context\ContextOptions;
use BWC\Component\JwtApi\Handler\Functional\KeyProviderHandler;

class KeyProviderHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldConstruct()
    {
        new KeyProviderHandler($this->getKeyProviderMock());
    }

    /**
     * @test
     */
    public function shouldSetKeysFromProviderToContext()
    {
        $contextMock = $this->getJwtContextMock();

        $expectedKeys = array('111', '222');

        $keyProviderMock = $this->getKeyProviderMock();
        $keyProviderMock->expects($this->once())
            ->method('getKeys')
            ->with($contextMock)
            ->will($this->returnValue($expectedKeys));

        $handler = new KeyProviderHandler($keyProviderMock);

        $handler->handleContext($contextMock);

        $this->assertEquals($expectedKeys, $contextMock->optionGet(ContextOptions::KEYS));
    }


    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\BWC\Component\JwtApi\KeyProvider\KeyProviderInterface
     */
    public function getKeyProviderMock()
    {
        return $this->getMock('BWC\Component\JwtApi\KeyProvider\KeyProviderInterface');
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\BWC\Component\JwtApi\Context\JwtContext
     */
    protected function getJwtContextMock()
    {
        return $this->getMock('BWC\Component\JwtApi\Context\JwtContext', array('none'), array(), '', false);
    }
} 