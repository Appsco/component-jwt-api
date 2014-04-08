<?php

namespace BWC\Component\JwtApiBundle\Manager;

use BWC\Component\JwtApiBundle\Context\JwtContext;
use BWC\Component\JwtApiBundle\Handler\Structural\CompositeContextHandler;
use BWC\Component\JwtApiBundle\Receiver\ReceiverInterface;
use BWC\Component\JwtApiBundle\Sender\SenderInterface;
use BWC\Component\JwtApiBundle\Strategy\Exception\ExceptionStrategyInterface;
use Symfony\Component\HttpFoundation\Request;


class JwtManager extends CompositeContextHandler implements JwtManagerInterface
{
    /** @var  ReceiverInterface */
    protected $receiver;

    /** @var  SenderInterface */
    protected $sender;



    /**
     * @param ReceiverInterface $receiver
     * @param SenderInterface $sender
     */
    public function __construct(ReceiverInterface $receiver, SenderInterface $sender)
    {
        $this->receiver = $receiver;
        $this->sender = $sender;
    }



    /**
     * @param Request $request
     * @throws \Exception
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handleRequest(Request $request)
    {
        $context = $this->receive($request);

        $this->handleContext($context);

        return $this->send($context);
    }




    /**
     * @param Request $request
     * @return JwtContext
     */
    protected function receive(Request $request)
    {
        return $this->receiver->receive($request);
    }


    /**
     * @param JwtContext $context
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function send(JwtContext $context)
    {
        return $this->sender->send($context);
    }


} 