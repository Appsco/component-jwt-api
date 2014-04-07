<?php

namespace BWC\Component\JwtApiBundle\Manager;

use BWC\Component\JwtApiBundle\Context\JwtContext;
use BWC\Component\JwtApiBundle\Handler\Structural\CompositeContextHandler;
use BWC\Component\JwtApiBundle\Receiver\ReceiverInterface;
use BWC\Component\JwtApiBundle\Sender\SenderInterface;
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

//        $context->setRequestJwt(
//                $this->decodeJwtString($context->getRequestJwtToken())
//        );
//
//        $this->afterReceive($request, $context);
//
//        $this->getKeys($context);
//
//        $this->validate($context);
//
//        $this->setSubject($context);
//
//        $handler = $this->getHandler($context->getRequestJwt());
//
//        $this->handleJwt($handler, $context);
//
//        $this->encode($context);
//
//        $response = $this->contextManager->send($context);
//
//        return $response;
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









//
//
//    /**
//     * @param string $jwtString
//     * @return MethodJwt
//     */
//    protected function decodeJwtString($jwtString)
//    {
//        $jwt = $this->jwtEncoder->decode($jwtString, '\BWC\Component\JwtApiBundle\Method\MethodJwt');
//
//        return $jwt;
//    }
//
//    /**
//     * @param Jwt $jwt
//     * @return HandlerInterface
//     * @throws \InvalidArgumentException
//     */
//    protected function getHandler(Jwt $jwt)
//    {
//        $result = @$this->handlers[$jwt->getType()];
//
//        if (!$result) {
//            throw new \InvalidArgumentException(sprintf("Invalid payload type '%s'", $jwt->getType()));
//        }
//
//        return $result;
//    }
//
//
//    /**
//     * @param JwtContext $context
//     * @throws JwtException
//     */
//    protected function getKeys(JwtContext $context)
//    {
//        $keys = $this->keyProvider->getKeys($context);
//
//        if (false == is_array($keys)) {
//            throw new JwtException('Expected array of keys');
//        }
//
//        $context->optionSet(ContextOptions::KEYS, $keys);
//    }
//
//    /**
//     * @param JwtContext $context
//     * @throws JwtException
//     */
//    protected function validate(JwtContext $context)
//    {
//        $jwt = $context->getRequestJwt();
//        if (false == $jwt instanceof Jose) {
//            throw new JwtException('Expected jwt to validate');
//        }
//
//        $keys = $context->optionGet(ContextOptions::KEYS);
//        if (false == is_array($keys)) {
//            throw new JwtException('Expected array of strings to validate jwt with');
//        }
//
//        $this->validator->validate($jwt, $keys);
//    }
//
//    /**
//     * @param JwtContext $context
//     * @return void
//     */
//    protected function setSubject(JwtContext $context)
//    {
//        $context->setSubject($this->subjectProvider->getSubject($context));
//    }
//
//    /**
//     * @param HandlerInterface $handler
//     * @param JwtContext $context
//     */
//    protected function handleJwt(HandlerInterface $handler, JwtContext $context)
//    {
//        $handler->handle($context);
//    }
//
//
//    protected function encode(JwtContext $context)
//    {
//        if ($context->getResponseJwt()) {
//            $keys = $context->optionGet(ContextOptions::KEYS);
//            if ($keys) {
//                $context->setResponseToken(
//                    $this->jwtEncoder->encode($context->getResponseJwt(), array_shift($keys))
//                );
//            }
//        }
//    }

} 