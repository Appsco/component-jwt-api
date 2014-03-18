<?php

namespace BWC\Component\JwtApi\Method;

use BWC\Component\JwtApi\Context\JwtContext;
use BWC\Component\JwtApi\HandlerInterface;


class MethodHandler implements HandlerInterface
{
    /**
     * methodName => MethodInterface
     * @var array MethodInterface[]
     */
    protected $methods = array();


    /**
     * @param JwtContext $context
     */
    public function handle(JwtContext $context)
    {
        $requestJwt = new MethodJwt($context->getRequestJwt()->getHeader(), $context->getRequestJwt()->getPayload());

        $context->setRequestJwt($requestJwt);

        $method = $this->getMethod($requestJwt->getMethod());

        $method->handle($context);
    }

    /**
     * @param string $methodName
     * @param MethodInterface $method
     * @throws \InvalidArgumentException
     */
    public function addMethod($methodName, MethodInterface $method)
    {
        if (!is_string($methodName)) {
            throw new \InvalidArgumentException('Method name must be string');
        }
        $this->methods[$methodName] = $method;
    }


    /**
     * @param string $methodName
     * @return MethodInterface
     * @throws \RuntimeException
     */
    protected function getMethod($methodName)
    {
        if (!isset($this->methods[$methodName])) {
            throw new \RuntimeException(sprintf("Invalid verb '%s'", $methodName));
        }

        return $this->methods[$methodName];
    }
} 