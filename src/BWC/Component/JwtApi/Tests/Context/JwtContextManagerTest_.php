<?php

namespace BWC\Component\JwtApi\Tests\Context;

use BWC\Component\JwtApi\Context\JwtBindingTypes;
use BWC\Component\JwtApi\Context\JwtContext;
use BWC\Component\JwtApi\Context\JwtContextManager;
use BWC\Component\JwtApi\Method\MethodJwt;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class JwtContextManagerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function shouldReceiveFromGet()
    {
        $expectedRequest = new Request(array('jwt'=>$expectedJwtToken = 'jwtToken.part2.part3'));

        $bearerProviderMock = $this->getBearerProviderMock();
        $bearerProviderMock->expects($this->once())
            ->method('getBearer')
            ->with()
            ->will($this->returnValue($expectedBearer = 'bearer'));

        $contextManager = new JwtContextManager($bearerProviderMock);

        $context = $contextManager->receive($expectedRequest);

        $this->assertEquals($expectedRequest, $context->getRequest());
        $this->assertEquals(JwtBindingTypes::HTTP_REDIRECT, $context->getRequestBindingType());
        $this->assertEquals($expectedJwtToken, $context->getRequestJwtToken());
        $this->assertEquals($expectedBearer, $context->getBearer());
    }


    /**
     * @test
     * @expectedException \RuntimeException
     * @expectedExceptionMessage No jwt found in request
     */
    public function shouldReceiveThrowIfNotJwtParam()
    {
        $expectedRequest = new Request();
        $bearerProviderMock = $this->getBearerProviderMock();

        $contextManager = new JwtContextManager($bearerProviderMock);

        $contextManager->receive($expectedRequest);
    }

    /**
     * @test
     */
    public function shouldReceiveFromPost()
    {
        $expectedRequest = new Request(array(), array('jwt'=>$expectedJwtToken = 'jwtToken.part2.part3'));

        $bearerProviderMock = $this->getBearerProviderMock();
        $bearerProviderMock->expects($this->once())
                ->method('getBearer')
                ->with()
                ->will($this->returnValue($expectedBearer = 'bearer'));

        $contextManager = new JwtContextManager($bearerProviderMock);

        $context = $contextManager->receive($expectedRequest);

        $this->assertEquals($expectedRequest, $context->getRequest());
        $this->assertEquals(JwtBindingTypes::HTTP_POST, $context->getRequestBindingType());
        $this->assertEquals($expectedJwtToken, $context->getRequestJwtToken());
        $this->assertEquals($expectedBearer, $context->getBearer());
    }


    /**
     * @test
     */
    public function shouldSendContentIfNoBearer()
    {
        $request = new Request();
        $requestToken = 'request.token.string';
        $bearer = null;

        $context = new JwtContext($request, JwtBindingTypes::HTTP_REDIRECT, $requestToken, $bearer);
        $context->setResponseToken($expectedResponseToken = 'response.jwt.token');

        $contextManager = new JwtContextManager($this->getBearerProviderMock());

        $response = $contextManager->send($context);

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $response);
        $this->assertNotInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $response);
        $this->assertEquals($expectedResponseToken, $response->getContent());
    }

    /**
     * @test
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Missing destination url
     */
    public function shouldThrowIfDestinationUrlNotSetAndBearerPresent()
    {
        $request = new Request();
        $requestToken = 'request.token.string';
        $bearer = 'bearer';

        $context = new JwtContext($request, JwtBindingTypes::HTTP_REDIRECT, $requestToken, $bearer);
        $context->setResponseToken($expectedResponseToken = 'response.jwt.token');

        $contextManager = new JwtContextManager($this->getBearerProviderMock());

        $contextManager->send($context);
    }

    /**
     * @test
     */
    public function shouldSendRedirectWhenRedirectBindingTypeSetToContext()
    {
        $request = new Request();
        $requestToken = 'request.token.string';
        $bearer = 'bearer';

        $context = new JwtContext($request, JwtBindingTypes::HTTP_REDIRECT, $requestToken, $bearer);
        $context->setResponseToken($expectedResponseToken = 'response.jwt.token');
        $context->setDestinationUrl($expectedDestination = 'http://example.com/reply/to');
        $context->setResponseBindingType(JwtBindingTypes::HTTP_REDIRECT);

        $contextManager = new JwtContextManager($this->getBearerProviderMock());

        /** @var RedirectResponse $response */
        $response = $contextManager->send($context);

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $response);
        $this->assertStringStartsWith($expectedDestination, $response->getTargetUrl());
        $this->assertStringEndsWith('jwt='.$expectedResponseToken, $response->getTargetUrl());
    }

    /**
     * @test
     */
    public function shouldSendPostWhenPostBindingTypeSetToContext()
    {
        $request = new Request();
        $requestToken = 'request.token.string';
        $bearer = 'bearer';

        $context = new JwtContext($request, JwtBindingTypes::HTTP_REDIRECT, $requestToken, $bearer);
        $context->setResponseToken($expectedResponseToken = 'response.jwt.token');
        $context->setDestinationUrl($expectedDestination = 'http://example.com/reply/to?a=1&b=2');
        $context->setResponseBindingType(JwtBindingTypes::HTTP_POST);

        $contextManager = new JwtContextManager($this->getBearerProviderMock());

        /** @var RedirectResponse $response */
        $response = $contextManager->send($context);

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $response);
        $this->assertNotInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $response);

        $c = new Crawler($response->getContent());

        $form = $c->filter('form');
        $this->assertEquals(1, $form->count());

        $form = $form->first();
        $this->assertEquals('POST', strtoupper($form->attr('method')));
        $this->assertEquals($expectedDestination, $form->attr('action'));

        $input = $form->filter('input[name="jwt"]');
        $this->assertEquals(1, $input->count());

        $input = $input->first();
        $this->assertEquals($expectedResponseToken, $input->attr('value'));
    }


    /**
     * @test
     */
    public function shouldSendDefaultToRedirectIfBearerSetAndResponseBindingNotSetAndResponseTokenShort()
    {
        $request = new Request();
        $requestToken = 'request.token.string';
        $bearer = 'bearer';

        $context = new JwtContext($request, JwtBindingTypes::HTTP_REDIRECT, $requestToken, $bearer);
        $context->setResponseToken($expectedResponseToken = 'response.jwt.token');
        $context->setDestinationUrl($expectedDestination = 'http://example.com/reply/to');

        $contextManager = new JwtContextManager($this->getBearerProviderMock());

        /** @var RedirectResponse $response */
        $response = $contextManager->send($context);

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $response);
    }


    /**
     * @test
     */
    public function shouldSendDefaultToPostIfBearerSetAndResponseBindingNotSetAndResponseTokenLong()
    {
        $request = new Request();
        $requestToken = 'request.token.string';
        $bearer = 'bearer';

        $context = new JwtContext($request, JwtBindingTypes::HTTP_REDIRECT, $requestToken, $bearer);

        $expectedResponseToken = str_pad('', 1500, 'x');
        $context->setResponseToken($expectedResponseToken);

        $context->setDestinationUrl($expectedDestination = 'http://example.com/reply/to');

        $contextManager = new JwtContextManager($this->getBearerProviderMock());

        /** @var RedirectResponse $response */
        $response = $contextManager->send($context);

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $response);
        $this->assertNotInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $response);

        $c = new Crawler($response->getContent());

        $form = $c->filter('form');
        $this->assertEquals(1, $form->count());

        $form = $form->first();
        $this->assertEquals('POST', strtoupper($form->attr('method')));
        $this->assertEquals($expectedDestination, $form->attr('action'));

        $input = $form->filter('input[name="jwt"]');
        $this->assertEquals(1, $input->count());

        $input = $input->first();
        $this->assertEquals($expectedResponseToken, $input->attr('value'));

    }


    /**
     * @test
     */
    public function shouldSendToRequestReplyTo()
    {
        $request = new Request();
        $requestToken = 'request.token.string';
        $bearer = 'bearer';

        $context = new JwtContext($request, JwtBindingTypes::HTTP_REDIRECT, $requestToken, $bearer);
        $context->setResponseToken($expectedResponseToken = 'response.jwt.token');

        $jwt = new MethodJwt();
        $jwt->setReplyTo($expectedReplyTo = 'http://example.com/reply/to');
        $context->setRequestJwt($jwt);

        $contextManager = new JwtContextManager($this->getBearerProviderMock());

        /** @var RedirectResponse $response */
        $response = $contextManager->send($context);

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $response);
        $this->assertStringStartsWith($expectedReplyTo, $response->getTargetUrl());
    }

    /**
     * @test
     */
    public function shouldSendToContextDestinationIfBothRequestReplyToAndContextDestinationSet()
    {
        $request = new Request();
        $requestToken = 'request.token.string';
        $bearer = 'bearer';

        $context = new JwtContext($request, JwtBindingTypes::HTTP_REDIRECT, $requestToken, $bearer);
        $context->setResponseToken($expectedResponseToken = 'response.jwt.token');

        $jwt = new MethodJwt();
        $jwt->setReplyTo($expectedReplyTo = 'http://example.com/reply/to');
        $context->setRequestJwt($jwt);

        $context->setDestinationUrl($expectedDestination = 'http://other.com/destination');

        $contextManager = new JwtContextManager($this->getBearerProviderMock());

        /** @var RedirectResponse $response */
        $response = $contextManager->send($context);

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $response);
        $this->assertStringStartsWith($expectedDestination, $response->getTargetUrl());
    }



    /**
     * @test
     */
    public function shouldTruncateReplyToQueryStringIfRedirectSend()
    {
        $request = new Request();
        $requestToken = 'request.token.string';
        $bearer = 'bearer';

        $context = new JwtContext($request, JwtBindingTypes::HTTP_REDIRECT, $requestToken, $bearer);
        $context->setResponseToken($expectedResponseToken = 'response.jwt.token');

        $jwt = new MethodJwt();
        $expectedReplyTo = 'http://example.com/reply/to';
        $jwt->setReplyTo($expectedReplyTo.'?a=1&b=2');
        $context->setRequestJwt($jwt);

        $contextManager = new JwtContextManager($this->getBearerProviderMock());

        /** @var RedirectResponse $response */
        $response = $contextManager->send($context);

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $response);
        $this->assertStringStartsWith($expectedReplyTo, $response->getTargetUrl());
    }

    /**
     * @test
     */
    public function shouldTruncateDestinationQueryStringIfRedirectSend()
    {
        $request = new Request();
        $requestToken = 'request.token.string';
        $bearer = 'bearer';

        $context = new JwtContext($request, JwtBindingTypes::HTTP_REDIRECT, $requestToken, $bearer);
        $context->setResponseToken($expectedResponseToken = 'response.jwt.token');
        $expectedDestination = 'http://example.com/reply/to';
        $context->setDestinationUrl($expectedDestination.'?a=1&b=2');

        $contextManager = new JwtContextManager($this->getBearerProviderMock());

        /** @var RedirectResponse $response */
        $response = $contextManager->send($context);

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $response);
        $this->assertStringStartsWith($expectedDestination, $response->getTargetUrl());
    }


    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\BWC\Component\JwtApi\Context\Bearer\BearerProviderInterface
     */
    protected function getBearerProviderMock()
    {
        return $this->getMock('BWC\Component\JwtApi\Context\Bearer\BearerProviderInterface');
    }


} 