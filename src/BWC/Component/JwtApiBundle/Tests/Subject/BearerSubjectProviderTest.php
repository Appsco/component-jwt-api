<?php

namespace BWC\Component\JwtApiBundle\Tests\Subject;

use BWC\Component\JwtApiBundle\Subject\BearerSubjectProvider;

class BearerSubjectProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldConstruct()
    {
        new BearerSubjectProvider();
    }
} 