<?php

namespace BWC\Component\JwtApiBundle\Tests\Method;

use BWC\Component\Jwe\JwsHeader;
use BWC\Component\Jwe\Jwt;
use BWC\Component\Jwe\JwtClaim;
use BWC\Component\JwtApiBundle\Method\Directions;
use BWC\Component\JwtApiBundle\Method\MethodClaims;
use BWC\Component\JwtApiBundle\Method\MethodJwt;

class MethodJwtTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldCreateFromJwt()
    {
        $jwt = new Jwt(
            array(JwsHeader::CRITICAL => $critical = array('foo')),
            array(JwtClaim::AUDIENCE => $audience = 'bar', MethodClaims::DATA => $data = array(1, 2, 3))
        );

        $methodJwt = MethodJwt::createFromJwt($jwt);

        $this->assertEquals($critical, $methodJwt->headerGet(JwsHeader::CRITICAL));
        $this->assertEquals($audience, $methodJwt->getAudience());
        $this->assertEquals($data, $methodJwt->getData());
    }

    /**
     * @test
     */
    public function shouldCreate()
    {
        $methodJwt = MethodJwt::create(
            $direction = Directions::REQUEST,
            $issuer = 'issuer',
            $method = 'method',
            $instance = 'instance',
            $data = array(1, 2, 3),
            $inResponseTo = 'foo'
        );

        $this->assertEquals($direction, $methodJwt->getDirection());
        $this->assertEquals($issuer, $methodJwt->getIssuer());
        $this->assertEquals($method, $methodJwt->getMethod());
        $this->assertEquals($instance, $methodJwt->getInstance());
        $this->assertEquals($data, $methodJwt->getData());
        $this->assertEquals($inResponseTo, $methodJwt->getInResponseTo());
    }
} 