<?php

namespace IlicMiljan\RetryMaster\Policy\Backoff;

/**
 * ## Fixed Backoff Policy
 *
 * FixedBackoffPolicy is an implementation of the BackoffPolicy interface
 * that provides a fixed backoff. This means that the wait time between
 * retry attempts is always the same, regardless of the number of attempts.
 *
 * This class is based on the concept of fixed backoff, which is a standard
 * error-handling strategy for network applications. The idea is to always wait
 * the same amount of time between retries, regardless of how many times the
 * operation has been retried. This can be useful in situations where the
 * likelihood of a retry succeeding is not related to the number of times it
 * has been tried, and where it's not necessary to increase the delay over time.
 *
 * @package IlicMiljan\RetryMaster\Policy\Backoff
 */
class FixedBackoffPolicy implements BackoffPolicy
{
    /**
     * @var int The fixed interval in milliseconds to wait between retries.
     */
    private int $intervalMilliseconds;

    /**
     * FixedBackoffPolicy constructor.
     *
     * @param int $intervalMilliseconds The fixed interval in milliseconds to
     *                                  wait between retries.
     */
    public function __construct(int $intervalMilliseconds = 1000)
    {
        $this->intervalMilliseconds = $intervalMilliseconds;
    }

    /**
     * Determines the backoff period in milliseconds for a given retry attempt.
     *
     * @param int $attempt The number of the current retry attempt (1 for the
     *                     first attempt, 2 for the second attempt, and so on).
     * @return int The backoff period in milliseconds, always the same for this
     *             policy.
     */
    public function backoff(int $attempt): int
    {
        return $this->intervalMilliseconds;
    }
}
