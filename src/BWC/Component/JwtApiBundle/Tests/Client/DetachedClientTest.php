<?php

namespace BWC\Component\JwtApiBundle\Tests\Client;

use BWC\Component\Jwe\JwsHeader;
use BWC\Component\Jwe\Jwt;
use BWC\Component\JwtApiBundle\Client\DetachedClient;
use BWC\Component\JwtApiBundle\Context\JwtBindingTypes;
use BWC\Component\JwtApiBundle\Method\MethodClaims;
use BWC\Component\JwtApiBundle\Method\MethodJwt;
use BWC\Share\Net\HttpStatusCode;

class DetachedClientTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function shouldConstruct()
    {
        new DetachedClient($this->getHttpClientMock(), 'issuer', 'targetUrl', 'key', $this->getEncoderMock());
    }

    /**
     * @test
     */
    public function shouldSendHttpPost()
    {
        $expectedIssuer = 'issuer';
        $expectedTargetUrl = 'targetUrl';
        $expectedKey ='key';

        $expectedResponseToken = 'response.jwt.token';
        $expectedResponseJwt = new Jwt(array('bar'=>456), array(MethodClaims::DATA => $expectedData = 'data'));

        $jwtRequest = new MethodJwt($expectedHeader = array(JwsHeader::TYPE => 'jose'), $expectedPayload = array('foo'=>123));

        $httpClientMock = $this->getHttpClientMock();
        $httpClientMock->expects($this->once())
            ->method('post')
            ->will($this->returnValue($expectedResponseToken));
        $httpClientMock->expects($this->once())
            ->method('getStatusCode')
            ->will($this->returnValue(HttpStatusCode::OK));

        $encoderMock = $this->getEncoderMock();
        $encoderMock->expects($this->once())
                ->method('encode')
                ->with($jwtRequest, $expectedKey)
                ->will($this->returnValue($expectedToken = 'jwt.token.foo'));
        $encoderMock->expects($this->once())
                ->method('decode')
                ->with($expectedResponseToken, $expectedKey)
                ->will($this->returnValue($expectedResponseJwt));


        $client = new DetachedClient($httpClientMock, $expectedIssuer, $expectedTargetUrl, $expectedKey, $encoderMock);

        $response = $client->send(JwtBindingTypes::HTTP_POST, $jwtRequest);

        $this->assertInstanceOf('BWC\Component\JwtApiBundle\Method\MethodJwt', $response);
        $this->assertEquals('JWT', $response->headerGet(JwsHeader::TYPE));
        $this->assertEquals(456, $response->headerGet('bar'));
        $this->assertEquals($expectedData, $response->getData());
    }


    /**
     * @test
     */
    public function shouldSendHttpRedirect()
    {
        $expectedIssuer = 'issuer';
        $expectedTargetUrl = 'targetUrl';
        $expectedKey ='key';

        $expectedResponseToken = 'response.jwt.token';
        $expectedResponseJwt = new Jwt(array('bar'=>456), array(MethodClaims::DATA => $expectedData = 'data'));

        $jwtRequest = new MethodJwt($expectedHeader = array(JwsHeader::TYPE => 'jose'), $expectedPayload = array('foo'=>123));

        $httpClientMock = $this->getHttpClientMock();
        $httpClientMock->expects($this->once())
                ->method('get')
                ->will($this->returnValue($expectedResponseToken));
        $httpClientMock->expects($this->once())
                ->method('getStatusCode')
                ->will($this->returnValue(HttpStatusCode::OK));

        $encoderMock = $this->getEncoderMock();
        $encoderMock->expects($this->once())
                ->method('encode')
                ->with($jwtRequest, $expectedKey)
                ->will($this->returnValue($expectedToken = 'jwt.token.foo'));
        $encoderMock->expects($this->once())
                ->method('decode')
                ->with($expectedResponseToken, $expectedKey)
                ->will($this->returnValue($expectedResponseJwt));


        $client = new DetachedClient($httpClientMock, $expectedIssuer, $expectedTargetUrl, $expectedKey, $encoderMock);

        $response = $client->send(JwtBindingTypes::HTTP_REDIRECT, $jwtRequest);

        $this->assertInstanceOf('BWC\Component\JwtApiBundle\Method\MethodJwt', $response);
        $this->assertEquals('JWT', $response->headerGet(JwsHeader::TYPE));
        $this->assertEquals(456, $response->headerGet('bar'));
        $this->assertEquals($expectedData, $response->getData());
    }

    /**
     * @test
     */
    public function shouldSendReturnEmptyJwtWhenEmptyResponse()
    {
        $expectedIssuer = 'issuer';
        $expectedTargetUrl = 'targetUrl';
        $expectedKey ='key';

        $expectedResponseToken = '';

        $jwtRequest = new MethodJwt($expectedHeader = array(JwsHeader::TYPE => 'jose'), $expectedPayload = array('foo'=>123));

        $httpClientMock = $this->getHttpClientMock();
        $httpClientMock->expects($this->once())
                ->method('get')
                ->will($this->returnValue($expectedResponseToken));
        $httpClientMock->expects($this->once())
                ->method('getStatusCode')
                ->will($this->returnValue(HttpStatusCode::OK));

        $encoderMock = $this->getEncoderMock();
        $encoderMock->expects($this->once())
                ->method('encode')
                ->with($jwtRequest, $expectedKey)
                ->will($this->returnValue($expectedToken = 'jwt.token.foo'));
        $encoderMock->expects($this->never())
                ->method('decode');


        $client = new DetachedClient($httpClientMock, $expectedIssuer, $expectedTargetUrl, $expectedKey, $encoderMock);

        $response = $client->send(JwtBindingTypes::HTTP_REDIRECT, $jwtRequest);

        $this->assertInstanceOf('BWC\Component\JwtApiBundle\Method\MethodJwt', $response);
        $this->assertEquals('JWT', $response->headerGet(JwsHeader::TYPE));
        $this->assertEquals(array(JwsHeader::TYPE=>'JWT'), $response->getHeader());
        $this->assertEquals(array(), $response->getPayload());
    }


    /**
     * @test
     * @expectedException \BWC\Component\JwtApiBundle\Error\RemoteMethodException
     * @expectedExceptionMessage error 123
     */
    public function shouldThrowRemoteExceptionIfGivenInResponseJwt()
    {
        $expectedIssuer = 'issuer';
        $expectedTargetUrl = 'targetUrl';
        $expectedKey ='key';

        $expectedResponseToken = 'response.jwt.token';
        $expectedResponseJwt = new Jwt(array(), array(MethodClaims::EXCEPTION => $expectedException = 'error 123'));

        $jwtRequest = new MethodJwt($expectedHeader = array(JwsHeader::TYPE => 'jose'), $expectedPayload = array('foo'=>123));

        $httpClientMock = $this->getHttpClientMock();
        $httpClientMock->expects($this->once())
                ->method('get')
                ->will($this->returnValue($expectedResponseToken));
        $httpClientMock->expects($this->once())
                ->method('getStatusCode')
                ->will($this->returnValue(HttpStatusCode::OK));

        $encoderMock = $this->getEncoderMock();
        $encoderMock->expects($this->once())
                ->method('encode')
                ->with($jwtRequest, $expectedKey)
                ->will($this->returnValue($expectedToken = 'jwt.token.foo'));
        $encoderMock->expects($this->once())
                ->method('decode')
                ->with($expectedResponseToken, $expectedKey)
                ->will($this->returnValue($expectedResponseJwt));


        $client = new DetachedClient($httpClientMock, $expectedIssuer, $expectedTargetUrl, $expectedKey, $encoderMock);

        $client->send(JwtBindingTypes::HTTP_REDIRECT, $jwtRequest);
    }

    /**
     * @test
     * @expectedException \RuntimeException
     * @expectedExceptionMessage API error: 500 response.jwt.token
     */
    public function shouldThrowOnHttpException()
    {
        $expectedIssuer = 'issuer';
        $expectedTargetUrl = 'targetUrl';
        $expectedKey ='key';

        $expectedResponseToken = 'response.jwt.token';

        $jwtRequest = new MethodJwt($expectedHeader = array(JwsHeader::TYPE => 'jose'), $expectedPayload = array('foo'=>123));

        $httpClientMock = $this->getHttpClientMock();
        $httpClientMock->expects($this->once())
                ->method('get')
                ->will($this->returnValue($expectedResponseToken));
        $httpClientMock->expects($this->once())
                ->method('getStatusCode')
                ->will($this->returnValue(HttpStatusCode::INTERNAL_SERVER_ERROR));

        $encoderMock = $this->getEncoderMock();
        $encoderMock->expects($this->once())
                ->method('encode')
                ->with($jwtRequest, $expectedKey)
                ->will($this->returnValue($expectedToken = 'jwt.token.foo'));
        $encoderMock->expects($this->never())
                ->method('decode');


        $client = new DetachedClient($httpClientMock, $expectedIssuer, $expectedTargetUrl, $expectedKey, $encoderMock);

        $client->send(JwtBindingTypes::HTTP_REDIRECT, $jwtRequest);
    }



    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\BWC\Share\Net\HttpClient\HttpClientInterface
     */
    protected function getHttpClientMock()
    {
        return $this->getMock('BWC\Share\Net\HttpClient\HttpClientInterface');
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\BWC\Component\Jwe\EncoderInterface
     */
    protected function getEncoderMock()
    {
        return $this->getMock('BWC\Component\Jwe\EncoderInterface');
    }

} 