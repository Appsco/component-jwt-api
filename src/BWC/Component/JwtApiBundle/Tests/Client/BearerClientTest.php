<?php

namespace BWC\Component\JwtApiBundle\Tests\Client;

use BWC\Component\Jwe\JwsHeader;
use BWC\Component\JwtApiBundle\Client\BearerClient;
use BWC\Component\JwtApiBundle\Context\JwtBindingTypes;
use BWC\Component\JwtApiBundle\Method\MethodJwt;
use Symfony\Component\DomCrawler\Crawler;


class BearerClientTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function shouldConstruct()
    {
        new BearerClient('replyToUrl', 'targetUrl', 'key', $this->getEncoderMock());
    }

    /**
     * @test
     */
    public function shouldSetReplyToUrl()
    {
        $client = new BearerClient($expectedReplyToUrl = 'replyToUrl', 'targetUrl', 'key', $this->getEncoderMock());

        $this->assertEquals($expectedReplyToUrl, $client->getReplyToUrl());

        $client->setReplyToUrl($expectedReplyToUrl = 'other');

        $this->assertEquals($expectedReplyToUrl, $client->getReplyToUrl());
    }


    /**
     * @test
     */
    public function shouldSendHttpRedirect()
    {
        $expectedReplyToUrl = 'replyToUrl';
        $expectedTargetUrl = 'targetUrl';
        $expectedKey ='key';

        $jwt = new MethodJwt($expectedHeader = array(JwsHeader::TYPE => 'jose'), $expectedPayload = array('foo'=>123));

        $encoderMock = $this->getEncoderMock();
        $encoderMock->expects($this->once())
            ->method('encode')
            ->with($jwt, $expectedKey)
            ->will($this->returnValue($expectedToken = 'jwt.token.foo'));

        $client = new BearerClient(
                $expectedReplyToUrl,
                $expectedTargetUrl,
                $expectedKey,
                $encoderMock
        );

        $response = $client->send(JwtBindingTypes::HTTP_REDIRECT, $jwt);

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $response);
        $this->assertStringStartsWith($expectedTargetUrl, $response->getTargetUrl());
        $this->assertStringEndsWith('?jwt='.$expectedToken, $response->getTargetUrl());
    }

    /**
     * @test
     */
    public function shouldSendHttpPost()
    {
        $expectedReplyToUrl = 'replyToUrl';
        $expectedTargetUrl = 'targetUrl';
        $expectedKey ='key';

        $jwt = new MethodJwt($expectedHeader = array(JwsHeader::TYPE => 'jose'), $expectedPayload = array('foo'=>123));

        $encoderMock = $this->getEncoderMock();
        $encoderMock->expects($this->once())
                ->method('encode')
                ->with($jwt, $expectedKey)
                ->will($this->returnValue($expectedToken = 'jwt.token.foo'));

        $client = new BearerClient(
                $expectedReplyToUrl,
                $expectedTargetUrl,
                $expectedKey,
                $encoderMock
        );

        $response = $client->send(JwtBindingTypes::HTTP_POST, $jwt);

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $response);
        $this->assertNotInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $response);

        $c = new Crawler($response->getContent());

        $form = $c->filter('form');
        $this->assertEquals(1, $form->count());
        $form = $form->first();
        $this->assertEquals($expectedTargetUrl, $form->attr('action'));
        $this->assertEquals('POST', strtoupper($form->attr('method')));

        $input = $form->filter('input[name="jwt"]');
        $this->assertEquals(1, $input->count());
        $input = $input->first();
        $this->assertEquals($expectedToken, $input->attr('value'));
    }







    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\BWC\Component\Jwe\EncoderInterface
     */
    protected function getEncoderMock()
    {
        return $this->getMock('BWC\Component\Jwe\EncoderInterface');
    }


} 