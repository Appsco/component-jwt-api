<?php

namespace BWC\Component\JwtApiBundle\Receiver;

use BWC\Component\JwtApiBundle\Context\JwtBindingTypes;
use BWC\Component\JwtApiBundle\Context\JwtContext;
use Symfony\Component\HttpFoundation\Request;


class JwtReceiver implements ReceiverInterface
{
    /**
     * @param Request $request
     * @throws \BWC\Component\JwtApiBundle\Error\JwtException
     * @return JwtContext|null
     */
    public function receive(Request $request)
    {
        $bindingType = JwtBindingTypes::HTTP_POST;
        $jwtToken = $request->request->get('jwt');
        if (!$jwtToken) {
            $jwtToken = $request->query->get('jwt');
            $bindingType = JwtBindingTypes::HTTP_REDIRECT;
        }

        return new JwtContext($request, $bindingType, $jwtToken);
    }

} 