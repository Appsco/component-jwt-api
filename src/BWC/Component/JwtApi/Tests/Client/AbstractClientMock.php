<?php

namespace BWC\Component\JwtApi\Tests\Client;

use BWC\Component\JwtApi\Client\AbstractClient;

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