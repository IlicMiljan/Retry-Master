<?php

namespace IlicMiljan\RetryMaster\Policy\Backoff;

use IlicMiljan\RetryMaster\Util\Random;
use IlicMiljan\RetryMaster\Util\RandomGenerator;

/**
 * ## Exponential Random Backoff Policy
 *
 * ExponentialRandomBackoffPolicy is an implementation of the BackoffPolicy
 * interface that provides an exponential backoff with a random component.
 * This means that the wait time between retry attempts increases exponentially
 * with each failed attempt, and also varies randomly within a range defined by
 * the current backoff interval.
 *
 * Adding a random component to the backoff helps to avoid situations where many
 * instances of an application all retry at the same time, potentially
 * overwhelming a system or service. This is known as the "thundering herd
 * problem".
 *
 * The maximum backoff interval can be configured to prevent the backoff from
 * growing indefinitely.
 *
 * @package IlicMiljan\RetryMaster\Policy\Backoff
 */
class ExponentialRandomBackoffPolicy implements BackoffPolicy
{
    /**
     * @var int The initial interval in milliseconds to wait before the first
     *          retry attempt.
     */
    private int $initialIntervalMilliseconds;

    /**
     * @var float The multiplier to use to generate the next backoff interval
     *            from the last one.
     */
    private float $multiplier;

    /**
     * @var int The maximum interval in milliseconds to wait between retries.
     */
    private int $maxIntervalMilliseconds;

    /**
     * @var Random The random number generator used by this policy.
     */
    private Random $random;

    /**
     * ExponentialRandomBackoffPolicy constructor.
     *
     * @param int $initialIntervalMilliseconds The initial interval in
     *                                         milliseconds to wait before the
     *                                         first retry attempt.
     * @param float $multiplier The multiplier to use to generate the next
     *                          backoff interval from the last one.
     * @param int $maxIntervalMilliseconds The maximum interval in milliseconds
     *                                      to wait between retries.
     */
    public function __construct(
        int $initialIntervalMilliseconds = 1000,
        float $multiplier = 2,
        int $maxIntervalMilliseconds = 30000
    ) {
        $this->initialIntervalMilliseconds = $initialIntervalMilliseconds;
        $this->multiplier = $multiplier;
        $this->maxIntervalMilliseconds = $maxIntervalMilliseconds;

        $this->random = new RandomGenerator(); // Default
    }

    /**
     * Determines the backoff period in milliseconds for a given retry attempt.
     *
     * @param int $attempt The number of the current retry attempt (1 for the
     *                     first attempt, 2 for the second attempt, and so on).
     * @return int The backoff period in milliseconds, calculated as a random
     * value between the initial interval and the current backoff interval.
     */
    public function backoff(int $attempt): int
    {
        $interval = intval(
            $this->initialIntervalMilliseconds * pow($this->multiplier, $attempt - 1)
        );

        $interval = min($interval, $this->maxIntervalMilliseconds);

        return $this->random->nextInt($interval, 2 * $interval);
    }

    /**
     * Sets the random number generator instance used by this policy.
     *
     * @param Random $random An instance of Random interface for generating random numbers.
     */
    public function setRandom(Random $random): void
    {
        $this->random = $random;
    }
}
