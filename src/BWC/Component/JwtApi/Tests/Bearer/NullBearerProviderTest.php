<?php

namespace BWC\Component\JwtApi\Tests\Bearer;

use BWC\Component\JwtApi\Bearer\NullBearerProvider;

class NullBearerProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldConstruct()
    {
        new NullBearerProvider();
    }

    /**
     * @test
     */
    public function shouldReturnNull()
    {
        $provider = new NullBearerProvider();
        $bearer = $provider->getBearer($this->getJwtContextMock());

        $this->assertNull($bearer);
    }



    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\BWC\Component\JwtApi\Context\JwtContext
     */
    protected function getJwtContextMock()
    {
        return $this->getMock('BWC\Component\JwtApi\Context\JwtContext', array(), array(), '', false);
    }
} 