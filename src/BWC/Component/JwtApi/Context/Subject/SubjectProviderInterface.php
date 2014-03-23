<?php

namespace BWC\Component\JwtApi\Context\Subject;

use BWC\Component\JwtApi\Context\JwtContext;

interface SubjectProviderInterface
{
    /**
     * @param JwtContext $context
     * @return mixed|null
     */
    public function getSubject(JwtContext $context);
} 