<?php

namespace BWC\Component\JwtApiBundle\Tests\Strategy\Exception;

use BWC\Component\JwtApiBundle\Strategy\Exception\CompositeExceptionStrategy;

class CompositeExceptionStrategyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldConstruct()
    {
        new CompositeExceptionStrategy();
    }

} 