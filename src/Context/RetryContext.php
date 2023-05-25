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
     * @var Exception[] The exceptions that were thrown during retry attempts.
     */
    private array $exceptions = [];

    /**
     * @var int The number of exceptions that occurred during retry attempts.
     */
    private int $exceptionCount = 0;

    /**
     * @var float The time (in microseconds as a float) when the context
     *            was created.
     */
    private float $startTime;

    public function __construct()
    {
        $this->startTime = microtime(true);
    }

    /**
     * Increments the retry count by one.
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
     * Adds an exception to the list of exceptions thrown during retry attempts.
     *
     * @param Exception $exception The exception to add.
     */
    public function addException(Exception $exception): void
    {
        $this->exceptions[] = $exception;
        $this->exceptionCount++;
    }

    /**
     * Gets the exceptions that were thrown during retry attempts.
     *
     * @return Exception[] The exceptions.
     */
    public function getExceptions(): array
    {
        return $this->exceptions;
    }

    /**
     * Gets the number of exceptions that occurred during retry attempts.
     *
     * @return int The exception count.
     */
    public function getExceptionCount(): int
    {
        return $this->exceptionCount;
    }

    /**
     * Gets the start time of the first retry attempt.
     *
     * @return float The start time in microseconds as a float, or null if
     *               no retry attempt has been made yet.
     */
    public function getStartTime(): float
    {
        return $this->startTime;
    }
}
