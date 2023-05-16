<?php

namespace IlicMiljan\RetryMaster\Statistics;

/**
 * Interface RetryStatistics
 *
 * RetryStatistics is an interface to be implemented by classes that track retry
 * operation metrics. It provides methods to increment the count of total
 * attempts, successful attempts, failed attempts, and the total sleep time
 * between retry attempts.
 *
 * It also provides methods to retrieve these statistics, which include the
 * total number of attempts, the number of successful attempts, the number of
 * failed attempts, and the total sleep time in milliseconds.
 *
 * This interface allows the statistics tracking to be customized, depending on
 * the needs of the application.
 *
 * @package IlicMiljan\RetryMaster\Statistics
 */
interface RetryStatistics
{
    /**
     * Increments the count of total retry attempts.
     */
    public function incrementTotalAttempts(): void;

    /**
     * Increments the count of successful retry attempts.
     */
    public function incrementSuccessfulAttempts(): void;

    /**
     * Increments the count of failed retry attempts.
     */
    public function incrementFailedAttempts(): void;

    /**
     * Increments the total sleep time between retry attempts.
     *
     * @param int $milliseconds The amount of sleep time in milliseconds to add
     *                          to the total.
     */
    public function incrementSleepTime(int $milliseconds): void;

    /**
     * Retrieves the total number of retry attempts.
     *
     * @return int The total number of retry attempts.
     */
    public function getTotalAttempts(): int;

    /**
     * Retrieves the number of successful retry attempts.
     *
     * @return int The number of successful retry attempts.
     */
    public function getSuccessfulAttempts(): int;

    /**
     * Retrieves the number of failed retry attempts.
     *
     * @return int The number of failed retry attempts.
     */
    public function getFailedAttempts(): int;

    /**
     * Retrieves the total sleep time between retry attempts in milliseconds.
     *
     * @return int The total sleep time in milliseconds.
     */
    public function getTotalSleepTimeMilliseconds(): int;
}
