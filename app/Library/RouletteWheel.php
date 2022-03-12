<?php

namespace App\Library;


class RouletteWheel
{

    /**
     * Pick a random element from an array based on its probability.
     *
     * @param $a
     *
     * @return int|string
     */
    public static function generate($a)
    {
        $sum = 0.0;
        $total = array_sum(array_values($a));
        $r = self::frandom();

        // just in case
        if ($r == $sum) { // in other words, r == 0
            return array_keys($a)[0];
        }

        foreach ($a as $key => $percentage) {
            $newsum = $sum + (float) $percentage / (float) $total;
            if ($r > $sum && $r <= $newsum) {
                return $key;
            }
            $sum = $newsum;
        }

        // just in case
        return array_keys($a)[sizeof($a) - 1];
    }

    /**
     * Generate a random float.
     *
     * @return float random
     */
    public static function frandom()
    {
        return (float) rand() / (float) getrandmax();
    }
}
