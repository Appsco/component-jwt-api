<?php

namespace BWC\Component\JwtApiBundle\Method;

final class Directions
{
    const REQUEST = 'req';
    const RESPONSE = 'resp';

    private static $validValues = array(self::REQUEST, self::RESPONSE);


    /**
     * @param string $value
     * @return bool
     */
    public static function isValid($value)
    {
        return in_array($value, self::$validValues);
    }



    private function __construct() { }
} 