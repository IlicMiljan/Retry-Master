<?php

namespace IlicMiljan\RetryMaster\Util;

use InvalidArgumentException;

/**
 * ## Random
 *
 * This interface represents a contract for generating random integers. It
 * provides a method that returns a random integer within a given range.
 *
 * @package IlicMiljan\RetryMaster\Util
 */
interface Random
{
    /**
     * Generates and returns a random integer between the provided minimum and
     * maximum values (inclusive).
     *
     * @param int $min The minimum value that the generated integer can be.
     * @param int $max The maximum value that the generated integer can be.
     *
     * @return int A random integer between $min and $max.
     *
     * @throws InvalidArgumentException If $min is greater than $max.
     */
    public function nextInt(int $min, int $max): int;
}
