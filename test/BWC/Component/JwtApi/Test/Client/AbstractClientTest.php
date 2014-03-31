<?php

namespace BWC\Component\JwtApi\Test\Client;

use BWC\Component\Jwe\Algorithm;
use BWC\Component\JwtApi\Context\JwtBindingType;


class AbstractClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldHaveDefaultBindingHttpPost()
    {
        $client = $this->getAbstractClientMock(array(
                $expectedIssuer = 'issuer',
                $expectedTargetUrl = 'target_url',
                $expectedKey = 'key',
                $expectedEncoder = $this->getEncoderMock()
        ));

        $this->assertEquals(JwtBindingType::HTTP_POST, $client->getDefaultBinding());
    }

    /**
     * @test
     */
    public function shouldSetDefaultBinding()
    {
        $client = $this->getAbstractClientMock(array(
                $expectedIssuer = 'issuer',
                $expectedTargetUrl = 'target_url',
                $expectedKey = 'key',
                $expectedEncoder = $this->getEncoderMock(),
                $expectedReplyTo = 'reply/to'
        ));

        $client->setDefaultBinding(JwtBindingType::HTTP_REDIRECT);

        $this->assertEquals(JwtBindingType::HTTP_REDIRECT, $client->getDefaultBinding());
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid binding type foo
     */
    public function shouldThrowOnSetInvalidDefaultBinding()
    {
        $client = $this->getAbstractClientMock(array(
                $expectedIssuer = 'issuer',
                $expectedTargetUrl = 'target_url',
                $expectedKey = 'key',
                $expectedEncoder = $this->getEncoderMock(),
                $expectedReplyTo = 'reply/to'
        ));

        $client->setDefaultBinding('foo');
    }

    /**
     * @test
     */
    public function shouldHaveDefaultAlgorithmHS512()
    {
        $client = $this->getAbstractClientMock(array(
                $expectedIssuer = 'issuer',
                $expectedTargetUrl = 'target_url',
                $expectedKey = 'key',
                $expectedEncoder = $this->getEncoderMock(),
                $expectedReplyTo = 'reply/to'
        ));

        $this->assertEquals(Algorithm::HS512, $client->getAlgorithm());
    }

    /**
     * @test
     */
    public function shouldSetAlgorithm()
    {
        $client = $this->getAbstractClientMock(array(
                $expectedIssuer = 'issuer',
                $expectedTargetUrl = 'target_url',
                $expectedKey = 'key',
                $expectedEncoder = $this->getEncoderMock(),
                $expectedReplyTo = 'reply/to'
        ));

        $client->setAlgorithm(Algorithm::HS384);

        $this->assertEquals(Algorithm::HS384, $client->getAlgorithm());
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid algorithm foo
     */
    public function shouldThrowOnSetInvalidAlgorithm()
    {
        $client = $this->getAbstractClientMock(array(
                $expectedIssuer = 'issuer',
                $expectedTargetUrl = 'target_url',
                $expectedKey = 'key',
                $expectedEncoder = $this->getEncoderMock(),
                $expectedReplyTo = 'reply/to'
        ));

        $client->setAlgorithm('foo');
    }

    /**
     * @test
     */
    public function shouldSetAudience()
    {
        $client = $this->getAbstractClientMock(array(
                $expectedIssuer = 'issuer',
                $expectedTargetUrl = 'target_url',
                $expectedKey = 'key',
                $expectedEncoder = $this->getEncoderMock(),
                $expectedReplyTo = 'reply/to'
        ));

        $client->setAudience($expectedAudience = 'foo');

        $this->assertEquals($expectedAudience, $client->getAudience());
    }

    /**
     * @test
     */
    public function shouldCheckBindingToDefaultIfNotSet()
    {
        $client = new AbstractClientMock('issuer', 'targetUrl', 'key', $this->getEncoderMock());

        $binding = null;
        $client->testCheckBinding($binding);

        $this->assertEquals($client->getDefaultBinding(), $binding);
    }

    /**
     * @test
     */
    public function shouldCheckBindingWithHttpPost()
    {
        $client = new AbstractClientMock('issuer', 'targetUrl', 'key', $this->getEncoderMock());

        $binding = JwtBindingType::HTTP_POST;
        $client->testCheckBinding($binding);

        $this->assertEquals(JwtBindingType::HTTP_POST, $binding);
    }

    /**
     * @test
     */
    public function shouldCheckBindingWithHttpRedirect()
    {
        $client = new AbstractClientMock('issuer', 'targetUrl', 'key', $this->getEncoderMock());

        $binding = JwtBindingType::HTTP_REDIRECT;
        $client->testCheckBinding($binding);

        $this->assertEquals(JwtBindingType::HTTP_REDIRECT, $binding);
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid binding type foo
     */
    public function shouldThrowOnCheckBindingWithInvalidBinding()
    {
        $client = new AbstractClientMock('issuer', 'targetUrl', 'key', $this->getEncoderMock());

        $binding = 'foo';
        $client->testCheckBinding($binding);
    }


    /**
     * @test
     */
    public function shouldGetRedirectUrlNotModifyIfNoQueryPart()
    {
        $client = new AbstractClientMock('issuer', $expectedUrl = 'targetUrl', 'key', $this->getEncoderMock());

        $url = $client->testGetRedirectUrl();

        $this->assertEquals($expectedUrl, $url);
    }

    /**
     * @test
     */
    public function shouldGetRedirectUrlTruncateQueryPart()
    {
        $expectedUrl = 'targetUrl';

        $client = new AbstractClientMock('issuer', $expectedUrl.'?a=1&b=2', 'key', $this->getEncoderMock());

        $url = $client->testGetRedirectUrl();

        $this->assertEquals($expectedUrl, $url);
    }




    /**
     * @param array $ctorParams
     * @return \PHPUnit_Framework_MockObject_MockObject|\BWC\Component\JwtApi\Client\AbstractClient
     */
    protected function getAbstractClientMock(array $ctorParams)
    {
        return $this->getMock('BWC\Component\JwtApi\Client\AbstractClient', array('foo'), $ctorParams, '', $ctorParams ? true : false);
    }


    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\BWC\Component\Jwe\EncoderInterface
     */
    protected function getEncoderMock()
    {
        return $this->getMock('BWC\Component\Jwe\EncoderInterface');
    }

} 