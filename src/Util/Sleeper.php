<?php

namespace IlicMiljan\RetryMaster\Util;

/**
 * Interface SleeperInterface
 *
 * The SleeperInterface represents a contract for a class that provides a method
 * for delaying the execution of a script for a specified number of
 * milliseconds.
 *
 * This interface can be implemented in a variety of contexts, such as
 * implementing backoff policies in retry operations, simulating network latency
 * in testing environments, or throttling requests to a third-party service.
 *
 * @package IlicMiljan\RetryMaster\Util
 */
interface Sleeper
{
    /**
     * Halts the execution of the script for a specified number of milliseconds.
     *
     * This method could use different techniques to pause the script,
     * which allows for more precise delay times than the sleep and usleep
     * functions, depending on the implementing class.
     *
     * @param int $milliseconds The number of milliseconds to sleep.
     */
    public function milliseconds(int $milliseconds): void;
}
