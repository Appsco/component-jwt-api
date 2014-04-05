<?php

namespace BWC\Component\JwtApi\Handler\Structural;

use BWC\Component\JwtApi\Context\JwtContext;
use BWC\Component\JwtApi\Error\JwtException;
use BWC\Component\JwtApi\Handler\ContextHandlerInterface;

class JwtPayloadFilterHandler implements ContextHandlerInterface
{
    /** @var ContextHandlerInterface  */
    protected $innerHandler;

    /** @var array  */
    protected $filter;


    /**
     * @param ContextHandlerInterface $innerHandler
     * @param array $filter
     */
    public function __construct(ContextHandlerInterface $innerHandler, array $filter)
    {
        $this->innerHandler = $innerHandler;
        $this->filter = $filter;
    }

    /**
     * @param JwtContext $context
     * @throws \BWC\Component\JwtApi\Error\JwtException
     */
    public function handleContext(JwtContext $context)
    {
        if (!$context->getRequestJwt()) {
            throw new JwtException('Missing request jwt to filter by');
        }
        foreach ($this->filter as $claim=>$value) {
            if ($context->getRequestJwt()->get($claim) != $value) {
                return;
            }
        }

        $this->innerHandler->handleContext($context);
    }


} 