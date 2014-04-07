<?php

namespace BWC\Component\JwtApiBundle\Subject;

use BWC\Component\JwtApiBundle\Context\JwtContext;

interface SubjectProviderInterface
{
    /**
     * @param JwtContext $context
     * @return mixed|null
     */
    public function getSubject(JwtContext $context);
} 