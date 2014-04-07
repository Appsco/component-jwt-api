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
        $expectedResponseJwt = new Jwt(array(), array(MethodClaims::EXCEPTION => $expectedException = 'error 123'));

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



    public function _testProfileInfoRedirect()
    {
        $profileInfo = new ProfileInfo(array(
            new ProfileAssertion($expectedResponseIssuer = 'response-issuer', $expectedAssertionId = 'id', array(
                new ProfileAttribute('foo', $fooValue='aaa'),
                new ProfileAttribute('bar', $barValue='bbb'),
            ))
        ));

        $json = json_encode($profileInfo);

        $expectedTargetUrl = 'http://example.com/target?a=1&b=2';

        $expectedKey = 'secret';

        $expectedInstance = 'instance';
        $expectedProfileCode = 'profile-code';
        $expectedData = array(1,2);
        $expectedJwtId = 'jwtID';

        $expectedBinding = JwtBindingTypes::HTTP_REDIRECT;

        $expectedRequestToken = 'sending.jwt.token';

        $expectedResponseToken = 'response.jwt.token';
        $expectedResponseJwt = new Jwt(array(), array(MethodClaims::DATA=>$json));

        $encoderMock = $this->getEncoderMock();
        $encoderMock->expects($this->once())
                ->method('encode')
                ->will($this->returnValue($expectedRequestToken));
        $encoderMock->expects($this->once())
                ->method('decode')
                ->with($expectedResponseToken, $expectedKey)
                ->will($this->returnValue($expectedResponseJwt));


        $httpClientMock = $this->getHttpClientMock();
        $httpClientMock->expects($this->once())
            ->method('get')
            ->will($this->returnValue($expectedResponseToken));


        $client = new DetachedClient(
                $expectedRequestIssuer = 'request-issuer',
                $expectedTargetUrl,
                $expectedKey,
                $encoderMock,
                $httpClientMock
        );

        /** @var \Appsco\My\Api\Model\ProfileInfo\ProfileInfo $response */
        $response = $client->profileInfo($expectedInstance, $expectedProfileCode, $expectedData, $expectedJwtId, $expectedBinding);

        $this->assertInstanceOf('Appsco\My\Api\Model\ProfileInfo\ProfileInfo', $response);

        $this->assertCount(1, $response->assertions);

        $assertion = $response->assertions[0];
        $this->assertEquals($expectedAssertionId, $assertion->id);
        $this->assertEquals($expectedResponseIssuer, $assertion->issuer);

        $this->assertCount(2, $assertion->attributes);
        $this->assertEquals('foo', $assertion->attributes[0]->name);
        $this->assertEquals($fooValue, $assertion->attributes[0]->value);
        $this->assertEquals('bar', $assertion->attributes[1]->name);
        $this->assertEquals($barValue, $assertion->attributes[1]->value);
    }




    public function _testProfileInfoPost()
    {
        $profileInfo = new ProfileInfo(array(
                new ProfileAssertion($expectedResponseIssuer = 'response-issuer', $expectedAssertionId = 'id', array(
                        new ProfileAttribute('foo', $fooValue='aaa'),
                        new ProfileAttribute('bar', $barValue='bbb'),
                ))
        ));

        $json = json_encode($profileInfo);

        $expectedTargetUrl = 'http://example.com/target?a=1&b=2';

        $expectedKey = 'secret';

        $expectedInstance = 'instance';
        $expectedProfileCode = 'profile-code';
        $expectedData = array(1,2);
        $expectedJwtId = 'jwtID';

        $expectedBinding = JwtBindingTypes::HTTP_POST;

        $expectedRequestToken = 'sending.jwt.token';

        $expectedResponseToken = 'response.jwt.token';
        $expectedResponseJwt = new Jwt(array(), array(MethodClaims::DATA=>$json));

        $encoderMock = $this->getEncoderMock();
        $encoderMock->expects($this->once())
                ->method('encode')
                ->will($this->returnValue($expectedRequestToken));
        $encoderMock->expects($this->once())
                ->method('decode')
                ->with($expectedResponseToken, $expectedKey)
                ->will($this->returnValue($expectedResponseJwt));


        $httpClientMock = $this->getHttpClientMock();
        $httpClientMock->expects($this->once())
                ->method('post')
                ->will($this->returnValue($expectedResponseToken));


        $client = new DetachedClient(
                $expectedRequestIssuer = 'request-issuer',
                $expectedTargetUrl,
                $expectedKey,
                $encoderMock,
                $httpClientMock
        );

        /** @var \Appsco\My\Api\Model\ProfileInfo\ProfileInfo $response */
        $response = $client->profileInfo($expectedInstance, $expectedProfileCode, $expectedData, $expectedJwtId, $expectedBinding);

        $this->assertInstanceOf('Appsco\My\Api\Model\ProfileInfo\ProfileInfo', $response);

        $this->assertCount(1, $response->assertions);

        $assertion = $response->assertions[0];
        $this->assertEquals($expectedAssertionId, $assertion->id);
        $this->assertEquals($expectedResponseIssuer, $assertion->issuer);

        $this->assertCount(2, $assertion->attributes);
        $this->assertEquals('foo', $assertion->attributes[0]->name);
        $this->assertEquals($fooValue, $assertion->attributes[0]->value);
        $this->assertEquals('bar', $assertion->attributes[1]->name);
        $this->assertEquals($barValue, $assertion->attributes[1]->value);
    }


    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage API error: 403 some error message
     */
    public function _testProfileInfoError()
    {
        $expectedTargetUrl = 'http://example.com/target?a=1&b=2';

        $expectedKey = 'secret';

        $expectedInstance = 'instance';
        $expectedProfileCode = 'profile-code';
        $expectedData = array(1,2);
        $expectedJwtId = 'jwtID';

        $expectedBinding = JwtBindingTypes::HTTP_POST;

        $expectedRequestToken = 'sending.jwt.token';

        $expectedResponseToken = 'some error message';

        $encoderMock = $this->getEncoderMock();
        $encoderMock->expects($this->once())
                ->method('encode')
                ->will($this->returnValue($expectedRequestToken));


        $httpClientMock = $this->getHttpClientMock();
        $httpClientMock->expects($this->once())
                ->method('post')
                ->will($this->returnValue($expectedResponseToken));
        $httpClientMock->expects($this->once())
                ->method('getStatusCode')
                ->will($this->returnValue(HttpStatusCode::FORBIDDEN));


        $client = new DetachedClient(
                $expectedRequestIssuer = 'request-issuer',
                $expectedTargetUrl,
                $expectedKey,
                $encoderMock,
                $httpClientMock
        );


        $client->profileInfo($expectedInstance, $expectedProfileCode, $expectedData, $expectedJwtId, $expectedBinding);
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