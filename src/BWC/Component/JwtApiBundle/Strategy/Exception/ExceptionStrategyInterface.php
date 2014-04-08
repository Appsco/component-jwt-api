<?php

namespace BWC\Component\JwtApiBundle\Strategy\Exception;

use BWC\Component\JwtApiBundle\Context\JwtContext;

interface ExceptionStrategyInterface
{
    /**
     * @param \Exception $exception
     * @param JwtContext $context
     * @return void
     */
    public function handle(\Exception $exception, JwtContext $context);
} 