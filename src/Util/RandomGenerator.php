<?php

namespace IlicMiljan\RetryMaster\Util;

use InvalidArgumentException;

/**
 * Class RandomGenerator
 *
 * The RandomGenerator class provides an implementation of the Random interface
 * using PHP's built-in rand() function. It is used to generate a random integer
 * between a given range.
 *
 * @package IlicMiljan\RetryMaster\Util
 */
class RandomGenerator implements Random
{
    /**
     * Generates and returns a random integer between the provided minimum and
     * maximum values (inclusive).
     *
     * @param int $min The minimum value that the generated integer can be.
     * @param int $max The maximum value that the generated integer can be.
     *
     * @return int A random integer between $min and $max
     */
    public function nextInt(int $min, int $max): int
    {
        return rand($min, $max);
    }
}
