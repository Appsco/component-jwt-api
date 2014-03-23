<?php

namespace BWC\Component\JwtApi\Method;

use BWC\Component\JwtApi\Context\JwtContext;


class CompositeMethod implements MethodInterface
{
    const OPTION_STOP_HANDLING = 'stop_handling';

    const OPTION_RAISE_EXCEPTION = 'raise_exception';

    /** @var MethodInterface[] */
    protected $methods = array();






    /**
     * @param JwtContext $context
     * @throws \Exception  If any of child methods put OPTION_RAISE_EXCEPTION in context
     */
    public function handle(JwtContext $context)
    {
        foreach ($this->methods as $method) {
            $method->handle($context);

            if ($context->optionGet(self::OPTION_STOP_HANDLING)) {
                break;
            }
        }

        if ($ex = $context->optionGet(self::OPTION_RAISE_EXCEPTION)) {
            throw $ex;
        }
    }


    /**
     * @param MethodInterface $method
     * @return CompositeMethod|$this
     */
    public function addMethod(MethodInterface $method)
    {
        $this->methods[] = $method;

        return $this;
    }

    /**
     * @return MethodInterface[]
     */
    public function getMethods()
    {
        return $this->methods;
    }

} 