<?php

namespace BWC\Component\JwtApi\Context\Bearer;

use Symfony\Component\HttpFoundation\Request;
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
     * @param Request $request
     * @return mixed|null
     */
    public function getBearer(Request $request)
    {
        $token = $this->securityContext->getToken();
        if (null == $token) {
            return null;
        }

        $user = $token->getUser();

        return $user;
    }

} 