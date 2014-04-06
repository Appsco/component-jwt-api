<?php

namespace BWC\Component\JwtApi\Tests\Handler\Functional;

use BWC\Component\JwtApi\Handler\Functional\ValidatorHandler;

class ValidatorHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldConstruct()
    {
        new ValidatorHandler($this->getValidatorMock());
    }

    /**
     * @test
     */
    public function shouldCallValidatorToValidate()
    {
        $contextMock = $this->getJwtContextMock();

        $validatorMock = $this->getValidatorMock();
        $validatorMock->expects($this->once())
            ->method('validate')
            ->with($contextMock);

        $handler = new ValidatorHandler($validatorMock);

        $handler->handleContext($contextMock);
    }


    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\BWC\Component\JwtApi\Validator\JwtValidatorInterface
     */
    public function getValidatorMock()
    {
        return $this->getMock('BWC\Component\JwtApi\Validator\JwtValidatorInterface');
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\BWC\Component\JwtApi\Context\JwtContext
     */
    protected function getJwtContextMock()
    {
        return $this->getMock('BWC\Component\JwtApi\Context\JwtContext', array('none'), array(), '', false);
    }
} 