<?php

namespace BWC\Component\JwtApiBundle\Strategy\Exception;

use BWC\Component\JwtApiBundle\Context\JwtContext;

class Rethrow implements ExceptionStrategyInterface
{
    /**
     * @param \Exception $exception
     * @param JwtContext $context
     * @throws \Exception
     * @return void
     */
    public function handle(\Exception $exception, JwtContext $context)
    {
        throw $exception;
    }

} 