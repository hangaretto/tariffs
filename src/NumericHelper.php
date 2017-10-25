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

    public static function plural($endings, $number)
    {
        $cases = [2, 0, 1, 1, 1, 2];
        $n = $number;
        return sprintf($endings[ ($n%100>4 && $n%100<20) ? 2 : $cases[min($n%10, 5)] ], $n);
    }
}