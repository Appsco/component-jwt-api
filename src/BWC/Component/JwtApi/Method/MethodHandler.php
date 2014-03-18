<?php

namespace BWC\Component\JwtApi\Method;

use BWC\Component\JwtApi\Context\JwtContext;
use BWC\Component\JwtApi\Event\JwtApiEvent;
use BWC\Component\JwtApi\Event\MethodEvent;
use BWC\Component\JwtApi\Event\MethodEvents;
use BWC\Component\JwtApi\HandlerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;


class MethodHandler implements HandlerInterface
{
    /**
     * methodName => MethodInterface
     * @var array MethodInterface[]
     */
    protected $methods = array();

    /** @var  EventDispatcherInterface|null */
    protected $eventDispatcher;



    /**
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     */
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @return null|\Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    public function getEventDispatcher()
    {
        return $this->eventDispatcher;
    }





    /**
     * @param JwtContext $context
     */
    public function handle(JwtContext $context)
    {
        $requestJwt = new MethodJwt($context->getRequestJwt()->getHeader(), $context->getRequestJwt()->getPayload());

        $context->setRequestJwt($requestJwt);

        $this->checkSubjectBearer($context);

        $method = $this->getMethod($requestJwt->getMethod());

        if ($this->dispatchBeforeHandle($context, $method)) {

            return;
        }

        $method->handle($context);

        $this->dispatchAfterHandle($context, $method);
    }


    /**
     * @param string $methodName
     * @param MethodInterface $method
     * @throws \InvalidArgumentException
     */
    public function addMethod($methodName, MethodInterface $method)
    {
        if (!is_string($methodName)) {
            throw new \InvalidArgumentException('Method name must be string');
        }
        $this->methods[$methodName] = $method;
    }


    /**
     * @param JwtContext $context
     * @throws \RuntimeException
     */
    protected function checkSubjectBearer(JwtContext $context)
    {
        if ($context->getBearer()) {
            if ($context->getRequestJwt()->getSubject()) {
                throw new \RuntimeException('Subject can not be specified if bearer is present');
            }
            $context->getRequestJwt()->setSubject($context->getBearer()->getSubject());
        }
    }

    /**
     * @param string $methodName
     * @return MethodInterface
     * @throws \RuntimeException
     */
    protected function getMethod($methodName)
    {
        if (!isset($this->methods[$methodName])) {
            throw new \RuntimeException(sprintf("Invalid verb '%s'", $methodName));
        }

        return $this->methods[$methodName];
    }


    /**
     * @param JwtContext $context
     * @param MethodInterface $method
     * @return bool
     */
    protected function dispatchBeforeHandle(JwtContext $context, MethodInterface $method)
    {
        if ($this->eventDispatcher) {
            $event = new MethodEvent($context, $method);
            $this->eventDispatcher->dispatch(MethodEvents::BEFORE_HANDLE, $event);

            if ($event->isHandled()) {

                return true;
            }
        }

        return false;
    }


    /**
     * @param JwtContext $context
     * @param MethodInterface $method
     * @return void
     */
    protected function dispatchAfterHandle(JwtContext $context, MethodInterface $method)
    {
        if ($this->eventDispatcher) {
            $event = new MethodEvent($context, $method);
            $this->eventDispatcher->dispatch(MethodEvents::AFTER_HANDLE, $event);
        }
    }

}
