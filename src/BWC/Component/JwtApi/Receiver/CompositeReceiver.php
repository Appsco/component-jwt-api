<?php

namespace BWC\Component\JwtApi\Receiver;

use BWC\Component\JwtApi\Context\JwtContext;
use Symfony\Component\HttpFoundation\Request;

class CompositeReceiver implements ReceiverInterface
{
    /** @var ReceiverInterface[] */
    protected $receivers = array();


    /**
     * @param Request $request
     * @return JwtContext|null
     */
    public function receive(Request $request)
    {
        $result = null;
        foreach ($this->receivers as $receiver) {
            $result = $receiver->receive($request);

            if ($result) {
                break;
            }
        }

        return $result;
    }


    /**
     * @param ReceiverInterface $receiver
     * @return CompositeReceiver|$this
     */
    public function addReceiver(ReceiverInterface $receiver) {
        $this->receivers[] = $receiver;

        return $this;
    }

} 