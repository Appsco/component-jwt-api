<?php

namespace BWC\Component\JwtApiBundle\Context;

final class JwtBindingTypes
{
    const HTTP_REDIRECT = 'http-redirect';

    const HTTP_POST = 'http-post';

    const CONTENT = 'content';



    static private $validValues = array(self::HTTP_REDIRECT, self::HTTP_POST, self::CONTENT);


    /**
     * @param string $bindingType
     * @return bool
     */
    static public function isValid($bindingType)
    {
        return in_array($bindingType, self::$validValues);
    }



    private function __construct() { }

} 