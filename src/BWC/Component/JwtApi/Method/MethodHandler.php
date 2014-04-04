<?php

namespace BWC\Component\JwtApi\Method;

use BWC\Component\JwtApi\Context\JwtContext;
use BWC\Component\JwtApi\Event\Method\MethodEvent;
use BWC\Component\JwtApi\Event\Method\MethodEvents;
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

        $method = $this->getMethod($requestJwt->getMethod());

        try {

            if ($this->dispatchBeforeHandle($context, $method)) {

                return;
            }

            $method->handle($context);

            $this->dispatchAfterHandle($context, $method);

        } catch (\Exception $ex) {

            $this->dispatchError($context, $method);

            $responseJwt = MethodJwt::create($requestJwt->getAudience(), null, $requestJwt->getMethod(), null, $requestJwt->getJwtId());
            $responseJwt->setException(sprintf('%s: %s', join('', array_slice(explode('\\', get_class($ex)), -1)), $ex->getMessage()));
            $responseJwt->setReplyTo($requestJwt->getReplyTo());

            $context->setResponseJwt($responseJwt);
        }
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

    /**
     * @param JwtContext $context
     * @param MethodInterface $method
     * @return void
     */
    protected function dispatchError(JwtContext $context, MethodInterface $method)
    {
        if ($this->eventDispatcher) {
            $event = new MethodEvent($context, $method);
            $this->eventDispatcher->dispatch(MethodEvents::ERROR, $event);
        }
    }

}
