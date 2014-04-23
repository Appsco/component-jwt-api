<?php

namespace BWC\Component\JwtApiBundle\Manager;

use BWC\Component\JwtApiBundle\Context\JwtContext;
use BWC\Component\JwtApiBundle\Handler\ContextHandlerInterface;
use BWC\Component\JwtApiBundle\Handler\Structural\CompositeContextHandler;
use BWC\Component\JwtApiBundle\Receiver\ReceiverInterface;
use BWC\Component\JwtApiBundle\Sender\SenderInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;


class JwtManager extends CompositeContextHandler implements JwtManagerInterface
{
    /** @var  ReceiverInterface */
    protected $receiver;

    /** @var  SenderInterface */
    protected $sender;

    /** @var  LoggerInterface|null */
    protected $logger;


    /**
     * @param ReceiverInterface $receiver
     * @param SenderInterface $sender
     * @param \Psr\Log\LoggerInterface|null $logger
     */
    public function __construct(ReceiverInterface $receiver, SenderInterface $sender, LoggerInterface $logger = null)
    {
        $this->receiver = $receiver;
        $this->sender = $sender;
        $this->logger = $logger;
    }


    /**
     * @param null|\Psr\Log\LoggerInterface $logger
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
    }

    /**
     * @return null|\Psr\Log\LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }



    /**
     * @param Request $request
     * @throws \Exception
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handleRequest(Request $request)
    {
        if ($this->logger) {
            $this->logger->debug('JwtManager.start', array('get'=>$request->request->all(), 'post'=>$request->query->all()));
        }

        $context = $this->receive($request);

        if ($this->logger) {
            $this->logger->debug('JwtManager.received', array('context'=>$context));
        }

        $this->handleContext($context);

        if ($this->logger) {
            $this->logger->debug('JwtManager.handled', array('context'=>$context));
        }

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


    /**
     * @return string
     */
    public function info()
    {
        $arr = array('* JwtManager');
        foreach ($this->getContextHandlers() as $child) {
            $this->getHandlerInfo($arr, $child, '    ');
        }
        return implode("\n", $arr);
    }


    /**
     * @param string[] $arr
     * @param ContextHandlerInterface $handler
     * @param string $indent
     */
    protected function getHandlerInfo(array &$arr, ContextHandlerInterface $handler, $indent)
    {
        $info = $handler->info();
        if (!$info) {
            $info = get_class($handler);
        }
        $arr[] = $indent.'* '.$info;
        if ($handler instanceof CompositeContextHandler) {
            foreach ($handler->getContextHandlers() as $child) {
                $this->getHandlerInfo($arr, $child, $indent.'    ');
            }
        }
    }

} 