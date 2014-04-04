<?php

namespace BWC\Component\JwtApi;

use BWC\Component\JwtApi\Context\JwtContext;
use BWC\Component\JwtApi\Context\JwtContextManagerInterface;
use BWC\Component\JwtApi\Context\Subject\SubjectProviderInterface;
use BWC\Component\JwtApi\Event\AfterReceive;
use BWC\Component\JwtApi\Event\Events;
use BWC\Component\JwtApi\KeyProvider\KeyProviderInterface;
use BWC\Component\Jwe\EncoderInterface;
use BWC\Component\Jwe\Jwt;
use BWC\Component\Jwe\JwtReceived;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;


class JwtHandlerService implements JwtHandlerServiceInterface
{
    /**
     * @var JwtContextManagerInterface
     */
    protected $contextManager;

    /**
     * @var  EncoderInterface
     */
    protected $jwtEncoder;

    /**
     * @var JwtValidatorInterface
     */
    protected $validator;

    /** @var SubjectProviderInterface  */
    protected $subjectProvider;

    /** @var null|\Symfony\Component\EventDispatcher\EventDispatcherInterface  */
    protected $dispatcher;

    /**
     * payloadType => HandlerInterface
     * @var HandlerInterface[]
     */
    protected $handlers = array();


    /**
     * @param JwtContextManagerInterface $contextManager
     * @param EncoderInterface $jwtEncoder
     * @param KeyProviderInterface $keyProvider
     * @param JwtValidatorInterface $validator
     * @param SubjectProviderInterface $subjectProvider
     * @param null|\Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher
     */
    public function __construct(
            JwtContextManagerInterface $contextManager,
            EncoderInterface $jwtEncoder,
            KeyProviderInterface $keyProvider,
            JwtValidatorInterface $validator,
            SubjectProviderInterface $subjectProvider,
            EventDispatcherInterface $dispatcher = null
    ) {
        $this->contextManager = $contextManager;
        $this->jwtEncoder = $jwtEncoder;
        $this->keyProvider = $keyProvider;
        $this->validator = $validator;
        $this->subjectProvider = $subjectProvider;
        $this->dispatcher = $dispatcher;
    }


    /**
     * @param string $type
     * @param HandlerInterface $handler
     * @throws \InvalidArgumentException
     */
    public function addHandler($type, HandlerInterface $handler)
    {
        if (!is_string($type)) {
            throw new \InvalidArgumentException('Type must be string');
        }
        $this->handlers[$type] = $handler;
    }


    /**
     * @param Request $request
     * @throws \Exception
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request)
    {
        $context = $this->receive($request);

        $context->setRequestJwt(
                $this->decodeJwtString($context->getRequestJwtToken())
        );

        $this->afterReceive($request, $context);

        $keys = $this->getKeys($context);

        $this->validateJwt($context->getRequestJwt(), $keys);

        $this->setSubject($context);

        $handler = $this->getHandler($context->getRequestJwt());

        $this->handleJwt($handler, $context);

        $this->encode($context, $keys);

        $response = $this->contextManager->send($context);

        return $response;
    }


    /**
     * @param Request $request
     * @param JwtContext $context
     */
    protected function afterReceive(Request $request, JwtContext $context = null)
    {
        if ($this->dispatcher) {
            $event = new AfterReceive($request, $context);
            $this->dispatcher->dispatch(Events::AFTER_RECEIVE, $event);
        }
    }


    /**
     * @param Request $request
     * @return JwtContext
     */
    protected function receive(Request $request)
    {
        $context = $this->contextManager->receive($request);

        return $context;
    }

    /**
     * @param string $jwtString
     * @return \BWC\Component\Jwe\JwtReceived
     */
    protected function decodeJwtString($jwtString)
    {
        $jwt = $this->jwtEncoder->decode($jwtString);

        return $jwt;
    }

    /**
     * @param Jwt $jwt
     * @return HandlerInterface
     * @throws \InvalidArgumentException
     */
    protected function getHandler(Jwt $jwt)
    {
        $result = @$this->handlers[$jwt->getType()];

        if (!$result) {
            throw new \InvalidArgumentException(sprintf("Invalid payload type '%s'", $jwt->getType()));
        }

        return $result;
    }


    /**
     * @param JwtContext $context
     * @return string[]
     */
    protected function getKeys(JwtContext $context)
    {
        $keys = $this->keyProvider->getKeys($context);

        return $keys;
    }

    /**
     * @param Jwt $jwt
     * @param string[] $keys
     * @throws \InvalidArgumentException
     */
    protected function validateJwt(Jwt $jwt, array $keys)
    {
        if (false == $jwt instanceof JwtReceived) {
            throw new \InvalidArgumentException('Expected JwtReceived');
        }
        /** @var $jwt JwtReceived */
        $this->validator->validate($jwt, $keys);
    }

    /**
     * @param JwtContext $context
     * @return void
     */
    protected function setSubject(JwtContext $context)
    {
        $context->setSubject($this->subjectProvider->getSubject($context));
    }

    /**
     * @param HandlerInterface $handler
     * @param JwtContext $context
     */
    protected function handleJwt(HandlerInterface $handler, JwtContext $context)
    {
        $handler->handle($context);
    }


    protected function encode(JwtContext $context, array $keys)
    {
        if ($context->getResponseJwt()) {
            $context->setResponseToken(
                $this->jwtEncoder->encode($context->getResponseJwt(), array_shift($keys))
            );
        }
    }
} 