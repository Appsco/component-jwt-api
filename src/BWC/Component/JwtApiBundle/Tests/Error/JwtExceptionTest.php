<?php

namespace BWC\Component\JwtApiBundle\Tests\Error;

use BWC\Component\JwtApiBundle\Error\JwtException;

class JwtExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldConstruct()
    {
        new JwtException();
    }
} 