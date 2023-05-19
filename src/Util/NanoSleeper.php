<?php

namespace IlicMiljan\RetryMaster\Util;

/**
 * Class NanoSleeper
 *
 * The NanoSleeper utility class is designed to provide a method for delaying
 * the execution of a script for a specified number of milliseconds.
 *
 * This class can be useful in a variety of contexts, such as implementing
 * backoff policies in retry operations, simulating network latency in testing
 * environments, or throttling requests to a third-party service.
 *
 * @package IlicMiljan\RetryMaster\Util
 */
class NanoSleeper implements Sleeper
{
    /**
     * Halts the execution of the script for a specified number of milliseconds.
     *
     * This method uses the time_nanosleep function to pause the script,
     * which allows for more precise delay times than the sleep and usleep
     * functions.
     *
     * @param int $milliseconds The number of milliseconds to sleep.
     */
    public function milliseconds(int $milliseconds): void
    {
        $seconds = floor($milliseconds / 1000);
        $nanoseconds = ($milliseconds % 1000) * 1000000;

        time_nanosleep(intval($seconds), $nanoseconds);
    }
}
