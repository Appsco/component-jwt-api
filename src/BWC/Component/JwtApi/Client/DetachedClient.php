<?php

namespace BWC\Component\JwtApi\Client;

use BWC\Component\Jwe\EncoderInterface;
use BWC\Component\JwtApi\Context\JwtBindingType;
use BWC\Component\JwtApi\Method\MethodJwt;
use BWC\Component\JwtApi\Method\RemoteMethodException;
use BWC\Share\Net\HttpClient\HttpClientInterface;
use BWC\Share\Net\HttpStatusCode;

class DetachedClient extends AbstractClient
{
    /** @var HttpClientInterface  */
    protected $httpClient;


    /**
     * @param HttpClientInterface $httpClient
     * @param string $issuer
     * @param string $targetUrl
     * @param string $key
     * @param EncoderInterface $encoder
     */
    public function __construct(HttpClientInterface $httpClient, $issuer, $targetUrl, $key, EncoderInterface $encoder)
    {
        parent::__construct($issuer, $targetUrl, $key, $encoder);

        $this->httpClient = $httpClient;
    }




    /**
     * @param string $binding
     * @param MethodJwt $jwt
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     * @return MethodJwt
     */
    public function send($binding, MethodJwt $jwt)
    {
        $this->checkBinding($binding);

        $token = $this->encoder->encode($jwt, $this->key, $this->getAlgorithm());

        if ($binding == JwtBindingType::HTTP_POST) {

            $response = $this->httpClient->post($this->targetUrl, array(), array('jwt'=>$token), 'application/jwt');

        } else if ($binding == JwtBindingType::HTTP_REDIRECT) {

            $response = $this->httpClient->get($this->getRedirectUrl(), array('jwt'=>$token));

        } else {

            throw new \InvalidArgumentException('Unsupported or invalid binding '.$binding);

        }

        $statusCode = $this->httpClient->getStatusCode();
        if ($statusCode != HttpStatusCode::OK) {
            throw new \RuntimeException(sprintf('API error: %s %s', $statusCode, $response));
        }

        if ($response) {
            $result = $this->encoder->decode($response, $this->key);
            $resultJwt = new MethodJwt($result->getHeader(), $result->getPayload());
        } else {
            $resultJwt = new MethodJwt();
        }


        if ($ex = $resultJwt->getException()) {
            throw new RemoteMethodException($ex);
        }

        return $resultJwt;
    }

} 