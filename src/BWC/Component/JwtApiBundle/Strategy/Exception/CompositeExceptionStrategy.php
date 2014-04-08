<?php

namespace BWC\Component\JwtApiBundle\Strategy\Exception;

use BWC\Component\JwtApiBundle\Context\JwtContext;

class CompositeExceptionStrategy implements ExceptionStrategyInterface
{
    /** @var ExceptionStrategyInterface[] */
    protected $strategies = array();


    /**
     * @param \Exception $exception
     * @param JwtContext $context
     */
    public function handle(\Exception $exception, JwtContext $context)
    {
        foreach ($this->strategies as $strategy) {
            $strategy->handle($exception, $context);
        }
    }


    /**
     * @param ExceptionStrategyInterface $strategy
     */
    public function addStrategy(ExceptionStrategyInterface $strategy)
    {
        $this->strategies[] = $strategy;
    }

} 