<?php

namespace BWC\Component\JwtApiBundle\Tests\Handler\Functional;

use BWC\Component\JwtApiBundle\Handler\Functional\SubjectProviderHandler;

class SubjectProviderHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldConstruct()
    {
        new SubjectProviderHandler($this->getSubjectProviderMock());
    }

    /**
     * @test
     */
    public function shouldSetSubjectFromProviderToContext()
    {
        $contextMock = $this->getJwtContextMock();

        $subjectProviderMock = $this->getSubjectProviderMock();
        $subjectProviderMock->expects($this->once())
            ->method('getSubject')
            ->with($contextMock)
            ->will($this->returnValue($expectedSubject = 'subject'));

        $handler = new SubjectProviderHandler($subjectProviderMock);

        $handler->handleContext($contextMock);

        $this->assertEquals($expectedSubject, $contextMock->getSubject());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\BWC\Component\JwtApiBundle\Subject\SubjectProviderInterface
     */
    public function getSubjectProviderMock()
    {
        return $this->getMock('BWC\Component\JwtApiBundle\Subject\SubjectProviderInterface');
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\BWC\Component\JwtApiBundle\Context\JwtContext
     */
    protected function getJwtContextMock()
    {
        return $this->getMock('BWC\Component\JwtApiBundle\Context\JwtContext', array('none'), array(), '', false);
    }
} 