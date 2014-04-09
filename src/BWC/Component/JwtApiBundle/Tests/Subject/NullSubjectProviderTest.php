<?php

namespace BWC\Component\JwtApiBundle\Tests\Subject;

use BWC\Component\JwtApiBundle\Subject\NullSubjectProvider;

class NullSubjectProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldConstruct()
    {
        new NullSubjectProvider();
    }
} 