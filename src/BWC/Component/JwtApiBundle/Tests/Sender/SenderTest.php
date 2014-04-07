<?php

namespace BWC\Component\JwtApiBundle\Tests\Sender;

use BWC\Component\JwtApiBundle\Context\JwtBindingTypes;
use BWC\Component\JwtApiBundle\Sender\Sender;
use Symfony\Component\DomCrawler\Crawler;

class SenderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldConstruct()
    {
        new Sender();
    }


    /**
     * @test
     */
    public function shouldDefaultBindingTypeToPostWhenBearerWithBigToken()
    {
        $bearer = 'bearer';
        $token = str_pad('', 2000, 'x');
        $destinationUrl = 'http://example/com';

        $contextMock = $this->getJwtContextMock();
        $contextMock->expects($this->any())
            ->method('getBearer')
            ->will($this->returnValue($bearer));
        $contextMock->expects($this->any())
            ->method('getResponseToken')
            ->will($this->returnValue($token));

        $contextMock->expects($this->any())
            ->method('getDestinationUrl')
            ->will($this->returnValue($destinationUrl));

        $sender = new Sender();

        $sender->send($contextMock);

        $this->assertEquals(JwtBindingTypes::HTTP_POST, $contextMock->getResponseBindingType());
    }

    /**
     * @test
     */
    public function shouldDefaultBindingTypeToRedirectWhenBearerWithSmallToken()
    {
        $bearer = 'bearer';
        $token = str_pad('', 100, 'x');
        $destinationUrl = 'http://example/com';

        $contextMock = $this->getJwtContextMock();
        $contextMock->expects($this->any())
            ->method('getBearer')
            ->will($this->returnValue($bearer));
        $contextMock->expects($this->any())
            ->method('getResponseToken')
            ->will($this->returnValue($token));

        $contextMock->expects($this->any())
            ->method('getDestinationUrl')
            ->will($this->returnValue($destinationUrl));

        $sender = new Sender();

        $sender->send($contextMock);

        $this->assertEquals(JwtBindingTypes::HTTP_REDIRECT, $contextMock->getResponseBindingType());
    }

    /**
     * @test
     */
    public function shouldDefaultBindingTypeToContextWhenNoBearer()
    {
        $bearer = null;

        $contextMock = $this->getJwtContextMock();
        $contextMock->expects($this->any())
            ->method('getBearer')
            ->will($this->returnValue($bearer));

        $sender = new Sender();

        $sender->send($contextMock);

        $this->assertEquals(JwtBindingTypes::CONTENT, $contextMock->getResponseBindingType());
    }


    /**
     * @test
     */
    public function shouldSendRedirect()
    {
        $destinationUrl = 'http://example.com';
        $token = 'token';

        $contextMock = $this->getJwtContextMock();
        $contextMock->setResponseBindingType(JwtBindingTypes::HTTP_REDIRECT);
        $contextMock->expects($this->any())
            ->method('getDestinationUrl')
            ->will($this->returnValue($destinationUrl.'?a=1&b=2'));
        $contextMock->expects($this->any())
            ->method('getResponseToken')
            ->will($this->returnValue($token));

        $sender = new Sender();

        /** @var \Symfony\Component\HttpFoundation\RedirectResponse $response */
        $response = $sender->send($contextMock);

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $response);
        $this->assertEquals($destinationUrl.'?jwt='.$token, $response->getTargetUrl());
    }


    /**
     * @test
     */
    public function shouldSendPost()
    {
        $destinationUrl = 'http://example.com/?a=1&b=2';
        $token = 'token';

        $contextMock = $this->getJwtContextMock();
        $contextMock->setResponseBindingType(JwtBindingTypes::HTTP_POST);
        $contextMock->expects($this->any())
            ->method('getDestinationUrl')
            ->will($this->returnValue($destinationUrl));
        $contextMock->expects($this->any())
            ->method('getResponseToken')
            ->will($this->returnValue($token));

        $sender = new Sender();

        /** @var \Symfony\Component\HttpFoundation\RedirectResponse $response */
        $response = $sender->send($contextMock);

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $response);
        $this->assertNotInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $response);

        $c = new Crawler($response->getContent());

        $form = $c->filter('form');
        $this->assertEquals(1, $form->count());
        $form = $form->first();
        $this->assertEquals($destinationUrl, $form->attr('action'));
        $this->assertEquals('POST', strtoupper($form->attr('method')));

        $input = $form->filter('input[name="jwt"]');
        $this->assertEquals(1, $input->count());
        $input = $input->first();
        $this->assertEquals($token, $input->attr('value'));
    }

    /**
     * @test
     */
    public function shouldSendContent()
    {
        $token = 'token';

        $contextMock = $this->getJwtContextMock();
        $contextMock->setResponseBindingType(JwtBindingTypes::CONTENT);
        $contextMock->expects($this->any())
            ->method('getResponseToken')
            ->will($this->returnValue($token));

        $sender = new Sender();

        /** @var \Symfony\Component\HttpFoundation\RedirectResponse $response */
        $response = $sender->send($contextMock);

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $response);
        $this->assertNotInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $response);

        $this->assertEquals($token, $response->getContent());
    }



    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\BWC\Component\JwtApiBundle\Context\JwtContext
     */
    protected function getJwtContextMock()
    {
        return $this->getMock('BWC\Component\JwtApiBundle\Context\JwtContext',
            array('getBearer', 'getResponseToken', 'getDestinationUrl'),
            array(), '', false);
    }
} 