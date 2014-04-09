<?php

namespace BWC\Component\JwtApiBundle\Tests\IssuerProvider;

use BWC\Component\JwtApiBundle\IssuerProvider\SimpleIssuerProvider;

class SimpleIssuerProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldConstructWithoutAnyParams()
    {
        new SimpleIssuerProvider();
    }

    /**
     * @test
     */
    public function shouldReturnEmptyStringWhenConstructedWithoutParameter()
    {
        $provider = new SimpleIssuerProvider();
        $this->assertEquals('', $provider->getIssuer($this->getJwtContextMock()));
    }

    /**
     * @test
     */
    public function shouldReturnIssuerSetInConstructor()
    {
        $provider = new SimpleIssuerProvider($expectedIssuer = 'issuer');
        $this->assertEquals($expectedIssuer, $provider->getIssuer($this->getJwtContextMock()));
    }

    /**
     * @test
     */
    public function shouldSetIssuer()
    {
        $provider = new SimpleIssuerProvider('issuer');
        $provider->setIssuer($expectedIssuer = 'other');
        $this->assertEquals($expectedIssuer, $provider->getIssuer($this->getJwtContextMock()));
    }



    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\BWC\Component\JwtApiBundle\Context\JwtContext
     */
    protected function getJwtContextMock()
    {
        return $this->getMock('BWC\Component\JwtApiBundle\Context\JwtContext', array('none'), array(), '', false);
    }


} 