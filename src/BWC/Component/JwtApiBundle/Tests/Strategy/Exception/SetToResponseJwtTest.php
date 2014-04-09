<?php

namespace BWC\Component\JwtApiBundle\Tests\Strategy\Exception;

use BWC\Component\JwtApiBundle\Strategy\Exception\SetToResponseJwt;

class SetToResponseJwtTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldConstruct()
    {
        new SetToResponseJwt();
    }
} 