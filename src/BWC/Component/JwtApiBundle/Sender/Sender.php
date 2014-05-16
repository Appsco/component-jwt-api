<?php

namespace BWC\Component\JwtApiBundle\Sender;

use BWC\Component\JwtApiBundle\Context\JwtBindingTypes;
use BWC\Component\JwtApiBundle\Context\JwtContext;
use BWC\Component\JwtApiBundle\Error\JwtException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;


class Sender implements SenderInterface
{
    /**
     * @param JwtContext $context
     * @throws \BWC\Component\JwtApiBundle\Error\JwtException
     * @return Response
     */
    public function send(JwtContext $context)
    {
        if (!$context->getResponseBindingType()) {
            $this->setResponseBindingType($context);
        }

        switch ($context->getResponseBindingType()) {

            case JwtBindingTypes::HTTP_REDIRECT:
                return $this->sendRedirect($context);

            case JwtBindingTypes::HTTP_POST:
                return $this->sendPost($context);

            case JwtBindingTypes::CONTENT:
                return $this->sendContent($context);

            default:
                throw new JwtException(sprintf("Unknown binding '%s'", $context->getResponseBindingType()));
        }
    }



    /**
     * @param JwtContext $context
     * @return void
     */
    protected function setResponseBindingType(JwtContext $context)
    {
        if ($context->getBearer()) {
            if (strlen($context->getResponseToken()) > 1200) {
                $context->setResponseBindingType(JwtBindingTypes::HTTP_POST);
            } else {
                $context->setResponseBindingType(JwtBindingTypes::HTTP_REDIRECT);
            }
        } else {
            $context->setResponseBindingType(JwtBindingTypes::CONTENT);
        }
    }




    /**
     * @param JwtContext $context
     * @return Response
     */
    protected function sendContent(JwtContext $context)
    {
        return new Response((string)$context->getResponseToken());
    }

    /**
     * @param JwtContext $context
     * @return Response
     */
    protected function sendRedirect(JwtContext $context)
    {
        $url = $this->getReplyUrl($context);

        if ($token = $context->getResponseToken()) {
            if (($pos = strpos($url, '?')) !== false) {
                $url = substr($url, 0, $pos);
            }
            $url = $url.'?jwt='.$token;
        }

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
    <meta http-equiv="content-type" content="charset=utf-8" />
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

    /**
     * @param JwtContext $context
     * @return string
     * @throws JwtException
     */
    protected function getReplyUrl(JwtContext $context)
    {
        $url = $context->getDestinationUrl();

        if (!$url && $methodJwt = $context->getRequestJwt()) {
            $url = $methodJwt->getReplyTo();
        }

        if (!$url) {
            throw new JwtException('Missing destination url');
        }

        return $url;
    }


} 