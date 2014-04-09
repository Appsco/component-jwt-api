<?php

namespace BWC\Component\JwtApiBundle\Tests\Strategy\Exception;

use BWC\Component\JwtApiBundle\Strategy\Exception\Rethrow;

class RethrowTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldConstruct()
    {
        new Rethrow();
    }

    /**
     * @test
     * @expectedException \RuntimeException
     * @expectedExceptionMessage foo
     */
    public function shouldRethrowException()
    {
        $rethrow = new Rethrow();

        $rethrow->handle(new \RuntimeException('foo'), $this->getJwtContextMock());
    }



    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\BWC\Component\JwtApiBundle\Context\JwtContext
     */
    protected function getJwtContextMock()
    {
        return $this->getMock('BWC\Component\JwtApiBundle\Context\JwtContext', array(), array(), '', false);
    }

} 