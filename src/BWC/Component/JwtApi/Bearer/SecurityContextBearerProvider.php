<?php

namespace BWC\Component\JwtApi\Bearer;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;

class SecurityContextBearerProvider implements BearerProviderInterface
{
    /** @var  SecurityContextInterface */
    protected $securityContext;

    /** @var  string */
    protected $userBearerClass;



    public function __construct(SecurityContextInterface $securityContext, $userBearerClass)
    {
        $this->securityContext = $securityContext;
        $this->userBearerClass = $userBearerClass;
    }



    /**
     * @param Request $request
     * @return BearerInterface|null
     */
    public function getBearer(Request $request)
    {
        $token = $this->securityContext->getToken();
        if (null == $token) {
            return null;
        }

        $user = $token->getUser();
        if (null == $user) {
            return null;
        }

        $class = $this->userBearerClass;

        return new $class($user);
    }

} 