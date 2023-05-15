<?php

namespace IlicMiljan\RetryMaster\Policy\Backoff;

/**
 * ## ExponentialBackoffPolicy
 *
 * ExponentialBackoffPolicy is an implementation of the BackoffPolicy interface
 * that provides an exponential backoff. This means that the wait time between
 * retry attempts increases exponentially with each failed attempt.
 *
 * This class is based on the concept of exponential backoff, which is a
 * standard error-handling strategy for network applications. The idea is to
 * increase the wait time between retries exponentially up to a maximum number
 * of retries, rather than retrying at a fixed rate (also known as "linear
 * backoff") or immediately in a loop (also known as "no backoff").
 *
 * @package IlicMiljan\RetryMaster\Policy\Backoff
 */
class ExponentialBackoffPolicy implements BackoffPolicy
{
    /**
     * @var int The initial interval in milliseconds to wait before the first
     * retry attempt.
     */
    private int $initialIntervalMilliseconds;

    /**
     * @var float The multiplier to use to generate the next backoff interval
     * from the last one.
     */
    private float $multiplier;

    /**
     * ExponentialBackoffPolicy constructor.
     *
     * @param int $initialIntervalMilliseconds The initial interval in
     * milliseconds to wait before the first retry attempt.
     * @param float $multiplier The multiplier to use to generate the next
     * backoff interval from the last one.
     */
    public function __construct(int $initialIntervalMilliseconds = 1000, float $multiplier = 2)
    {
        $this->initialIntervalMilliseconds = $initialIntervalMilliseconds;
        $this->multiplier = $multiplier;
    }

    /**
     * Determines the backoff period in milliseconds for a given retry attempt.
     *
     * @param int $attempt The number of the current retry attempt (1 for the
     * first attempt, 2 for the second attempt, and so on).
     * @return int The backoff period in milliseconds, calculated as
     * initialIntervalMilliseconds * (multiplier ^ (attempt - 1)).
     */
    public function backoff(int $attempt): int
    {
        return intval(
            $this->initialIntervalMilliseconds * pow($this->multiplier, $attempt - 1)
        );
    }
}
