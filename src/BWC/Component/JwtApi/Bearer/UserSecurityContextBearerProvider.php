<?php

namespace BWC\Component\JwtApi\Bearer;

use BWC\Component\JwtApi\Context\JwtContext;
use Symfony\Component\Security\Core\SecurityContextInterface;


class UserSecurityContextBearerProvider implements BearerProviderInterface
{
    /** @var  SecurityContextInterface */
    protected $securityContext;



    public function __construct(SecurityContextInterface $securityContext)
    {
        $this->securityContext = $securityContext;
    }



    /**
     * @param JwtContext $context
     * @return mixed|null
     */
    public function getBearer(JwtContext $context)
    {
        $token = $this->securityContext->getToken();
        if (null == $token) {
            return null;
        }

        $user = $token->getUser();

        return $user;
    }

} 