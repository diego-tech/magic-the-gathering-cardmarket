<?php

namespace App\Http\Helpers;

class AuxFunctions
{
    /**
     * Generate Random Password
     * 
     * @param string $char
     * @param int $length
     * @return string $combinationRandom
     */
    public static function randomPassword($char, $length)
    {
        $combinationRandom = "";

        for ($i = 0; $i < $length; $i++) {
            $combinationRandom .= substr(str_shuffle($char), 0, $length);
        }

        return $combinationRandom;
    }
}
