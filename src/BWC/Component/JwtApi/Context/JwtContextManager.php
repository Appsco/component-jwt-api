<?php

namespace BWC\Component\JwtApi\Context;

use BWC\Component\JwtApi\Context\Bearer\BearerProviderInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class JwtContextManager implements JwtContextManagerInterface
{
    /** @var  BearerProviderInterface */
    protected $bearerProvider;



    /**
     * @param BearerProviderInterface $bearerProvider
     */
    public function __construct(BearerProviderInterface $bearerProvider)
    {
        $this->bearerProvider = $bearerProvider;
    }



    /**
     * @param Request $request
     * @throws \RuntimeException
     * @return JwtContext
     */
    public function receive(Request $request)
    {
        $bindingType = JwtBindingType::HTTP_POST;
        $jwtToken = $request->request->get('jwt');
        if (!$jwtToken) {
            $jwtToken = $request->query->get('jwt');
            $bindingType = JwtBindingType::HTTP_REDIRECT;
        }

        if (!$jwtToken) {
            throw new \RuntimeException('No jwt found in request');
        }

        return new JwtContext($request, $bindingType, $jwtToken, $this->bearerProvider->getBearer($request));
    }


    /**
     * @param JwtContext $context
     * @throws \RuntimeException
     * @return Response
     */
    public function send(JwtContext $context)
    {
        if (!$context->getResponseBindingType()) {
            $this->setResponseBindingType($context);
        }

        switch ($context->getResponseBindingType()) {

            case JwtBindingType::HTTP_REDIRECT:
                return $this->sendRedirect($context);

            case JwtBindingType::HTTP_POST:
                return $this->sendPost($context);

            case JwtBindingType::CONTENT:
                return $this->sendContent($context);

            default:
                throw new \RuntimeException('Unknown binding '.$context->getResponseBindingType());
        }
    }


    /**
     * Sets $context responseBindingType
     * @param JwtContext $context
     * @return void
     */
    protected function setResponseBindingType(JwtContext $context)
    {
        if ($context->getBearer()) {
            if (strlen($context->getResponseToken()) > 1200) {
                $context->setResponseBindingType(JwtBindingType::HTTP_POST);
            } else {
                $context->setResponseBindingType(JwtBindingType::HTTP_REDIRECT);
            }
        } else {
            $context->setResponseBindingType(JwtBindingType::CONTENT);
        }
    }


    /**
     * @param JwtContext $context
     * @return string
     * @throws \RuntimeException
     */
    protected function getReplyUrl(JwtContext $context)
    {
        $url = $context->getDestinationUrl();
        if (!$url && $methodJwt = $context->getRequestJwtAsMethodJwt()) {
            $url = $methodJwt->getReplyTo();
        }
        if (!$url) {
            throw new \RuntimeException('Missing destination url');
        }

        return $url;
    }


    /**
     * @param JwtContext $context
     * @return Response
     */
    protected function sendContent(JwtContext $context)
    {
        return new Response($context->getResponseToken());
    }

    /**
     * @param JwtContext $context
     * @return Response
     */
    protected function sendRedirect(JwtContext $context)
    {
        $url = $this->getReplyUrl($context);
        if (($pos = strpos($url, '?')) !== false) {
            $url = substr($url, 0, $pos);
        }

        $url = $url.'?jwt='.$context->getResponseToken();

        return new RedirectResponse($url);
    }


    /**
     * @param JwtContext $context
     * @throws \InvalidArgumentException
     * @return Response
     */
    protected function sendPost(JwtContext $context)
    {
        $url = htmlentities($this->getReplyUrl($context));
        $token = htmlentities($context->getResponseToken());

        $html = <<<EOT
<!doctype html>
<html>
<head>
    <meta http-equiv="content-type" content="application/jwt; charset=utf-8" />
    <title>POST data</title>
</head>
<body onload="document.getElementsByTagName('input')[0].click();">
<noscript>
    <p><strong>Note:</strong> Since your browser does not support JavaScript, you must press the button below once to proceed.</p>
</noscript>
<form method="POST" action="$url">
    <input type="submit" style="display:none;" />
    <input type="hidden" name="jwt" value="$token"/>
<noscript>
    <input type="submit" value="Submit" />
</noscript>
</form>
</body>
</html>
EOT;

        return new Response($html);
    }





} 