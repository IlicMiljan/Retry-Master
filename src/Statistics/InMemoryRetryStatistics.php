<?php

namespace IlicMiljan\RetryMaster\Statistics;

/**
 * Class InMemoryRetryStatistics
 *
 * InMemoryRetryStatistics is a class that implements the RetryStatistics
 * interface and stores all statistical data in memory.
 *
 * This class is used for counting and retrieving retry operation metrics such
 * as the total number of attempts, the number of successful attempts, the
 * number of failed attempts, and the total sleep time between retry attempts.
 *
 * This class is primarily intended for use cases where the statistics do not
 * need to persist across multiple application instances or requests, as the
 * data is lost when the process ends.
 *
 * @package IlicMiljan\RetryMaster\Statistics
 */
class InMemoryRetryStatistics implements RetryStatistics
{
    private int $totalAttempts = 0;
    private int $successfulAttempts = 0;
    private int $failedAttempts = 0;
    private int $totalSleepTimeMilliseconds = 0;

    /**
     * Increments the count of total retry attempts.
     */
    public function incrementTotalAttempts(): void
    {
        $this->totalAttempts++;
    }

    /**
     * Increments the count of successful retry attempts.
     */
    public function incrementSuccessfulAttempts(): void
    {
        $this->successfulAttempts++;
    }

    /**
     * Increments the count of failed retry attempts.
     */
    public function incrementFailedAttempts(): void
    {
        $this->failedAttempts++;
    }

    /**
     * Increments the total sleep time between retry attempts.
     *
     * @param int $milliseconds The amount of sleep time in milliseconds to add
     *                          to the total.
     */
    public function incrementSleepTime(int $milliseconds): void
    {
        $this->totalSleepTimeMilliseconds += $milliseconds;
    }

    /**
     * Retrieves the total number of retry attempts.
     *
     * @return int The total number of retry attempts.
     */
    public function getTotalAttempts(): int
    {
        return $this->totalAttempts;
    }

    /**
     * Retrieves the number of successful retry attempts.
     *
     * @return int The number of successful retry attempts.
     */
    public function getSuccessfulAttempts(): int
    {
        return $this->successfulAttempts;
    }

    /**
     * Retrieves the number of failed retry attempts.
     *
     * @return int The number of failed retry attempts.
     */
    public function getFailedAttempts(): int
    {
        return $this->failedAttempts;
    }

    /**
     * Retrieves the total sleep time between retry attempts in milliseconds.
     *
     * @return int The total sleep time in milliseconds.
     */
    public function getTotalSleepTimeMilliseconds(): int
    {
        return $this->totalSleepTimeMilliseconds;
    }
}
