<?php

namespace BWC\Component\JwtApi\Test;

use BWC\Component\Jwe\Jwt;
use BWC\Component\Jwe\JwtReceived;
use BWC\Component\JwtApi\Context\JwtBindingType;
use BWC\Component\JwtApi\Context\JwtContext;
use BWC\Component\JwtApi\JwtHandlerService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class JwtHandlerServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldHandle()
    {
        $expectedJwtString = 'jwtString';

        $request = new Request(array('jwt'=>$expectedJwtString));

        $contextManagerMock = $this->getContextManagerMock();
        $encoderMock = $this->getJwtDecoderMock();
        $keyProviderMock = $this->getKeyProviderMock();
        $validatorMock = $this->getValidatorMock();
        $handlerMock = $this->getHandlerMock();

        $expectedJwt = new JwtReceived(
                $expectedSigningInput = 'signingInput',
                $expectedSignature = 'signature'
        );
        $expectedJwt->setType($expectedType = 'type_one');

        $expectedJoseResult = new Jwt();

        $contextManagerMock->expects($this->once())
            ->method('receive')
            ->with($request)
            ->will($this->returnValue(
                $expectedContext = new JwtContext($request, JwtBindingType::HTTP_REDIRECT, $expectedJwtString, null)
            ));

        $encoderMock->expects($this->once())
            ->method('decode')
            ->with($expectedJwtString)
            ->will($this->returnValue($expectedJwt));


        $keyProviderMock->expects($this->once())
            ->method('getKeys')
            ->with($expectedJwt)
            ->will($this->returnValue($expectedKeys = array('key1', 'key2')));

        $validatorMock->expects($this->once())
            ->method('validate')
            ->with($expectedJwt, $expectedKeys);

        $expectedResponseJwt = new Jwt();

        $handlerMock->expects($this->once())
            ->method('handle')
            ->with($expectedContext)
            ->will($this->returnCallback(
                function(JwtContext $context) use ($expectedResponseJwt)
                {
                    $context->setResponseJwt($expectedResponseJwt);
                }
            ));

        $encoderMock->expects($this->once())
            ->method('encode')
            ->with($expectedJoseResult, $expectedKeys[0])
            ->will($this->returnValue($expectedResultToken = 'result_token'));

        $contextManagerMock->expects($this->once())
            ->method('send')
            ->with($expectedContext)
            ->will($this->returnValue($expectedResponse = new Response($expectedResultToken)));

        $handlerService = new JwtHandlerService($contextManagerMock, $encoderMock, $keyProviderMock, $validatorMock);
        $handlerService->addHandler($expectedType, $handlerMock);

        $response = $handlerService->handle($request);

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $response);
        $this->assertEquals($expectedResultToken, $response->getContent());
        $this->assertEquals(200, $response->getStatusCode());
    }


    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\BWC\Component\JwtApi\Context\JwtContextManagerInterface
     */
    protected function getContextManagerMock()
    {
        return $this->getMock('BWC\Component\JwtApi\Context\JwtContextManagerInterface');
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\BWC\Component\Jwe\Encoder
     */
    protected function getJwtDecoderMock()
    {
        return $this->getMock('BWC\Component\Jwe\Encoder');
    }


    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\BWC\Component\JwtApi\KeyProvider\KeyProviderInterface
     */
    protected function getKeyProviderMock()
    {
        return $this->getMock('BWC\Component\JwtApi\KeyProvider\KeyProviderInterface');
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\BWC\Component\JwtApi\JwtValidatorInterface
     */
    protected function getValidatorMock()
    {
        return $this->getMock('BWC\Component\JwtApi\JwtValidatorInterface');
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\BWC\Component\JwtApi\HandlerInterface
     */
    protected function getHandlerMock()
    {
        return $this->getMock('BWC\Component\JwtApi\HandlerInterface');
    }

} 