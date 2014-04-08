<?php

namespace BWC\Component\JwtApiBundle\Handler\Structural;

use BWC\Component\JwtApiBundle\Context\JwtContext;
use BWC\Component\JwtApiBundle\Error\JwtException;
use BWC\Component\JwtApiBundle\Handler\ContextHandlerInterface;

class JwtPayloadFilterHandler extends CompositeContextHandler
{
    /** @var array  */
    protected $filter;


    /**
     * @param ContextHandlerInterface $innerHandler
     * @param array $filter
     */
    public function __construct(ContextHandlerInterface $innerHandler, array $filter)
    {
        $this->addContextHandler($innerHandler);
        $this->filter = $filter;
    }

    /**
     * @param JwtContext $context
     * @throws \BWC\Component\JwtApiBundle\Error\JwtException
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

        parent::handleContext($context);
    }

    /**
     * @return string
     */
    public function info()
    {
        return 'JwtPayloadFilterHandler - filter: '.json_encode($this->filter);
    }


}