<?php

namespace BWC\Component\JwtApiBundle\Receiver;

use BWC\Component\JwtApiBundle\Context\JwtContext;
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