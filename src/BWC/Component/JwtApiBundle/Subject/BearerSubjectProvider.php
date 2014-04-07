<?php

namespace BWC\Component\JwtApiBundle\Subject;

use BWC\Component\JwtApiBundle\Context\JwtContext;


class BearerSubjectProvider implements SubjectProviderInterface
{

    /**
     * @param JwtContext $context
     * @return mixed|null
     */
    public function getSubject(JwtContext $context)
    {
        return $context->getBearer();
    }

} 