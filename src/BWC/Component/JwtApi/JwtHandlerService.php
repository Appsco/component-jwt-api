<?php

namespace BWC\Component\JwtApi;

use BWC\Component\JwtApi\Context\JwtContext;
use BWC\Component\JwtApi\Context\JwtContextManagerInterface;
use BWC\Component\JwtApi\KeyProvider\KeyProviderInterface;
use BWC\Component\Jwe\EncoderInterface;
use BWC\Component\Jwe\Jwt;
use BWC\Component\Jwe\JwtReceived;
use Symfony\Component\HttpFoundation\Request;


class JwtHandlerService
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
     */
    public function __construct(
            JwtContextManagerInterface $contextManager,
            EncoderInterface $jwtEncoder,
            KeyProviderInterface $keyProvider,
            JwtValidatorInterface $validator
    ) {
        $this->contextManager = $contextManager;
        $this->jwtEncoder = $jwtEncoder;
        $this->keyProvider = $keyProvider;
        $this->validator = $validator;
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
     * @throws JwtException
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request)
    {
        $context = $this->contextManager->receive($request);

        $context->setRequestJwt(
                $this->decodeJwtString($context->getRequestJwtToken())
        );

        $handler = $this->getHandler($context->getRequestJwt());

        $keys = $this->getKeysForJwt($context->getRequestJwt());

        $this->validateJwt($context->getRequestJwt(), $keys);

        $this->handleJwt($handler, $context);

        $response = $this->contextManager->send($context);

        return $response;
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
     * @param Jwt $jwt
     * @return string[]
     */
    protected function getKeysForJwt(Jwt $jwt)
    {
        $keys = $this->keyProvider->getKeys($jwt);

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
     * @param HandlerInterface $handler
     * @param JwtContext $context
     */
    protected function handleJwt(HandlerInterface $handler, JwtContext $context)
    {
        $handler->handle($context);
    }


} 