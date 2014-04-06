<?php

namespace BWC\Component\JwtApi\Tests\Bearer;

use BWC\Component\JwtApi\Bearer\UserSecurityContextBearerProvider;

class UserSecurityContextBearerProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldConstruct()
    {
        new UserSecurityContextBearerProvider($this->getSecurityContextMock());
    }

    /**
     * @test
     */
    public function shouldReturnTokenUserIfTokenSet()
    {
        $expectedUser = 'user';

        $tokenMock = $this->getTokenMock();
        $tokenMock->expects($this->once())
            ->method('getUser')
            ->will($this->returnValue($expectedUser));

        $securityContextMock = $this->getSecurityContextMock();
        $securityContextMock->expects($this->once())
            ->method('getToken')
            ->will($this->returnValue($tokenMock));

        $jwtContextMock = $this->getJwtContextMock();

        $provider = new UserSecurityContextBearerProvider($securityContextMock);

        $bearer = $provider->getBearer($jwtContextMock);

        $this->assertEquals($expectedUser, $bearer);
    }

    /**
     * @test
     */
    public function shouldReturnNullIfNotToken()
    {
        $securityContextMock = $this->getSecurityContextMock();
        $securityContextMock->expects($this->once())
            ->method('getToken')
            ->will($this->returnValue(null));

        $jwtContextMock = $this->getJwtContextMock();

        $provider = new UserSecurityContextBearerProvider($securityContextMock);

        $bearer = $provider->getBearer($jwtContextMock);

        $this->assertNull($bearer);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Symfony\Component\Security\Core\SecurityContextInterface
     */
    protected function getSecurityContextMock()
    {
        return $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\BWC\Component\JwtApi\Context\JwtContext
     */
    protected function getJwtContextMock()
    {
        return $this->getMock('BWC\Component\JwtApi\Context\JwtContext', array(), array(), '', false);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Symfony\Component\Security\Core\Authentication\Token\TokenInterface
     */
    protected function getTokenMock()
    {
        return $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
    }
}