<?php

namespace BWC\Component\JwtApi\Bearer;

use Symfony\Component\Security\Core\User\UserInterface;

abstract class UserBearer implements BearerInterface
{
    /** @var  UserInterface */
    protected $user;


    public function __construct(UserInterface $user)
    {
        $this->user = $user;
    }


    /**
     * @param UserInterface $user
     * @return string The JWT subject string
     */
    abstract protected function getSubjectFromUser(UserInterface $user);



    /**
     * @return \Symfony\Component\Security\Core\User\UserInterface
     */
    public function getUser()
    {
        return $this->user;
    }


    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->getSubjectFromUser($this->user);
    }


} 