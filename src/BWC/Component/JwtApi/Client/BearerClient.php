<?php

namespace BWC\Component\JwtApi\Client;

use BWC\Component\Jwe\EncoderInterface;
use BWC\Component\JwtApi\Context\JwtBindingType;
use BWC\Component\JwtApi\Method\MethodJwt;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;


class BearerClient extends AbstractClient
{
    /** @var string|null */
    protected $replyToUrl;


    /**
     * @param string $replyToUrl
     * @param string $issuer
     * @param string $targetUrl
     * @param string $key
     * @param EncoderInterface $encoder
     */
    public function __construct($replyToUrl, $issuer, $targetUrl, $key, EncoderInterface $encoder)
    {
        parent::__construct($issuer, $targetUrl, $key, $encoder);

        $this->replyToUrl = $replyToUrl;
    }




    /**
     * @param null|string $replyToUrl
     */
    public function setReplyToUrl($replyToUrl)
    {
        $this->replyToUrl = $replyToUrl;
    }

    /**
     * @return null|string
     */
    public function getReplyToUrl()
    {
        return $this->replyToUrl;
    }


    // ---------------------------------------------


    /**
     * @param string $binding
     * @param MethodJwt $jwt
     * @return RedirectResponse|Response
     * @throws \InvalidArgumentException
     */
    public function send($binding, MethodJwt $jwt)
    {
        $this->checkBinding($binding);

        $token = $this->encoder->encode($jwt, $this->key, $this->getAlgorithm());

        if ($binding == JwtBindingType::HTTP_REDIRECT) {

            return new RedirectResponse($this->getRedirectUrl().'?jwt='.$token);

        } else if ($binding == JwtBindingType::HTTP_POST) {

            return $this->post($this->targetUrl, $token);

        } else {
            throw new \InvalidArgumentException('Unsupported or invalid binding '.$binding);
        }
    }


    /**
     * @param string $url
     * @param string $token
     * @return Response
     */
    protected function post($url, $token)
    {
        $url = htmlentities($url);
        $token = htmlentities($token);

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