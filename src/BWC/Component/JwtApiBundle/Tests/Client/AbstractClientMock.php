<?php

namespace BWC\Component\JwtApiBundle\Tests\Client;

use BWC\Component\JwtApiBundle\Client\AbstractClient;

class AbstractClientMock extends AbstractClient
{

    /**
     * @return string
     */
    public function testGetRedirectUrl()
    {
        return parent::getRedirectUrl();
    }

    /**
     * @param $binding
     */
    public function testCheckBinding(&$binding)
    {
        parent::checkBinding($binding);
    }

} 