<?php

namespace BWC\Component\JwtApiBundle\Client;

use BWC\Component\Jwe\EncoderInterface;
use BWC\Component\JwtApiBundle\Context\JwtBindingTypes;
use BWC\Component\JwtApiBundle\Method\MethodJwt;
use BWC\Component\JwtApiBundle\Error\RemoteMethodException;
use BWC\Share\Net\HttpClient\HttpClientInterface;
use BWC\Share\Net\HttpStatusCode;

class DetachedClient extends AbstractClient
{
    /** @var HttpClientInterface  */
    protected $httpClient;


    /**
     * @param HttpClientInterface $httpClient
     * @param string $targetUrl
     * @param string $key
     * @param EncoderInterface $encoder
     */
    public function __construct(HttpClientInterface $httpClient, $targetUrl, $key, EncoderInterface $encoder)
    {
        parent::__construct($targetUrl, $key, $encoder);

        $this->httpClient = $httpClient;
    }



    /**
     * @return \BWC\Share\Net\HttpClient\HttpClientInterface
     */
    public function getHttpClient()
    {
        return $this->httpClient;
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

        if ($binding == JwtBindingTypes::HTTP_POST) {

            $response = $this->httpClient->post($this->targetUrl, array(), array('jwt'=>$token), 'application/jwt');

        } else if ($binding == JwtBindingTypes::HTTP_REDIRECT) {

            $response = $this->httpClient->get($this->getRedirectUrl(), array('jwt'=>$token));

        } else {

            throw new \InvalidArgumentException('Unsupported or invalid binding '.$binding);

        }

        $statusCode = $this->httpClient->getStatusCode();
        if ($statusCode != HttpStatusCode::OK) {
            throw new \RuntimeException(sprintf('API error: %s %s', $statusCode, $response));
        }

        $resultJwt = null;

        if ($response) {
            try {
                $result = $this->encoder->decode($response, $this->key);
                $resultJwt = new MethodJwt($result->getHeader(), $result->getPayload());
            } catch (\Exception $ex) { }
        }

        if (!$resultJwt) {
            $resultJwt = new MethodJwt();
        }


        if ($ex = $resultJwt->getException()) {
            throw new RemoteMethodException($ex);
        }

        return $resultJwt;
    }

} 