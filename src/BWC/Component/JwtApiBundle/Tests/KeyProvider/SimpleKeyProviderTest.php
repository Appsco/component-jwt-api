<?php

namespace BWC\Component\JwtApiBundle\Tests\KeyProvider;

use BWC\Component\JwtApiBundle\KeyProvider\SimpleKeyProvider;

class SimpleKeyProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldConstruct()
    {
        new SimpleKeyProvider();
    }

    /**
     * @test
     */
    public function shouldReturnKeysConstructedWith()
    {
        $expectedKeys = array('111', '222');

        $provider = new SimpleKeyProvider($expectedKeys);

        $this->assertEquals($expectedKeys, $provider->getKeys($this->getJwtContextMock()));
    }

    /**
     * @test
     */
    public function shouldAddKey()
    {
        $expectedKeys = array('111', '222');
        $addedKey = '333';

        $provider = new SimpleKeyProvider($expectedKeys);
        $provider->addKey($addedKey);

        $keys = $provider->getKeys($this->getJwtContextMock());

        $this->assertCount(3, $keys);
        $this->assertEquals($expectedKeys, array_intersect($expectedKeys, $keys));
        $this->assertEquals(array($addedKey), array_intersect(array($addedKey), $keys));
    }


    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\BWC\Component\JwtApiBundle\Context\JwtContext
     */
    protected function getJwtContextMock()
    {
        return $this->getMock('BWC\Component\JwtApiBundle\Context\JwtContext', array('none'), array(), '', false);
    }

} 