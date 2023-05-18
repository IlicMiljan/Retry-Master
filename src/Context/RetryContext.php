<?php

namespace IlicMiljan\RetryMaster\Context;

use Exception;

/**
 * ## Retry Context
 *
 * This class represents the context for a retry operation. It holds information
 * about the number of attempts made, the last exception that was thrown, and
 * the time the first retry attempt was made.
 *
 * @package IlicMiljan\RetryMaster\Context
 */
class RetryContext
{
    /**
     * @var int The number of retry attempts made.
     */
    private int $retryCount = 0;

    /**
     * @var Exception|null The last exception that was thrown during a retry
     *                     attempt.
     */
    private ?Exception $lastException = null;

    /**
     * @var float|null The time (in microseconds as a float) when the first
     *                 retry attempt was made.
     */
    private ?float $startTime = null;

    public function start(): void
    {
        if ($this->retryCount == 0) {
            $this->startTime = microtime(true);
        }

        // TODO: Throw Exception
    }

    /**
     * Increments the retry count by one. If this is the first retry attempt,
     * also sets the start time.
     */
    public function incrementRetryCount(): void
    {
        $this->retryCount++;
    }

    /**
     * Gets the current retry count.
     *
     * @return int The current retry count.
     */
    public function getRetryCount(): int
    {
        return $this->retryCount;
    }

    /**
     * Sets the last exception that was thrown during a retry attempt.
     *
     * @param Exception $exception The exception to set as the last exception.
     */
    public function setLastException(Exception $exception): void
    {
        $this->lastException = $exception;
    }

    /**
     * Gets the last exception that was thrown during a retry attempt.
     *
     * @return Exception|null The last exception, or null if no exception has
     * been set yet.
     */
    public function getLastException(): ?Exception
    {
        return $this->lastException;
    }

    /**
     * Gets the start time of the first retry attempt.
     *
     * @return float|null The start time in microseconds as a float, or null if
     * no retry attempt has been made yet.
     */
    public function getStartTime(): ?float
    {
        return $this->startTime;
    }
}
