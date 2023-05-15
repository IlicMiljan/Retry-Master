<?php

namespace IlicMiljan\RetryMaster\Policy\Backoff;

/**
 * Class UniformRandomBackoffPolicy
 *
 * UniformRandomBackoffPolicy is an implementation of the BackoffPolicy
 * interface that provides a uniform random backoff. This means that the wait
 * time between retry attempts is a random number uniformly distributed between
 * a minimum and maximum interval.
 *
 * This class can be used in scenarios where you want to introduce a random
 * delay between retries to avoid a thundering herd problem - a scenario where
 * multiple processes become ready to retry at the same time, potentially
 * overloading the system. The random backoff can help to spread out the retries
 * over time.
 *
 * @package IlicMiljan\RetryMaster\Policy\Backoff
 */
class UniformRandomBackoffPolicy implements BackoffPolicy
{
    /**
     * @var int The minimum interval in milliseconds to wait between retries.
     */
    private int $minIntervalMilliseconds;

    /**
     * @var int The maximum interval in milliseconds to wait between retries.
     */
    private int $maxIntervalMilliseconds;

    /**
     * UniformRandomBackoffPolicy constructor.
     *
     * @param int $minIntervalMilliseconds The minimum interval in milliseconds
     *                                     to wait between retries.
     * @param int $maxIntervalMilliseconds The maximum interval in milliseconds
     *                                     to wait between retries.
     */
    public function __construct(int $minIntervalMilliseconds = 1000, int $maxIntervalMilliseconds = 2000)
    {
        $this->minIntervalMilliseconds = $minIntervalMilliseconds;
        $this->maxIntervalMilliseconds = $maxIntervalMilliseconds;
    }

    /**
     * Determines the backoff period in milliseconds for a given retry attempt.
     *
     * @param int $attempt The number of the current retry attempt (1 for the
     *                     first attempt, 2 for the second attempt, and so on).
     * @return int The backoff period in milliseconds, which is a random value
     *             between the minimum and maximum interval for this policy.
     */
    public function backoff(int $attempt): int
    {
        return rand($this->minIntervalMilliseconds, $this->maxIntervalMilliseconds);
    }
}
