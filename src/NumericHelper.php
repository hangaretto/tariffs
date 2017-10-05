<?php
/**
 * Created by PhpStorm.
 * User: vadim
 * Date: 23.08.17
 * Time: 9:33
 */

namespace Magnetar\Tariffs;

class NumericHelper
{
    /**
     * Number to negative.
     *
     * @params int|float $number
     * @return int|float
     */
    public static function toNegative($num)
    {
        return $num < 0 ? $num : -1*$num;
    }

    /**
     * Number to positive.
     *
     * @params int|float $number
     * @return int|float
     */
    public static function toPositive($num)
    {
        return $num > 0 ? $num : -1*$num;
    }
}