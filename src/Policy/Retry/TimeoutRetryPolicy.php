<?php

namespace IlicMiljan\RetryMaster\Policy\Retry;

use Exception;
use IlicMiljan\RetryMaster\Context\RetryContext;

/**
 * ## TimeoutRetryPolicy
 *
 * TimeoutRetryPolicy is an implementation of RetryPolicy that
 * decides to retry a failed operation based on the total elapsed time
 * since the first attempt.
 *
 * It is initialized with a timeout in milliseconds, and the shouldRetry method
 * will return true if the elapsed time since the first attempt is less
 * than the configured timeout.
 *
 * @package IlicMiljan\RetryMaster\Policy\Retry
 */
class TimeoutRetryPolicy implements RetryPolicy
{
    private int $timeoutMilliseconds;

    /**
     * @param int $timeoutMilliseconds The timeout in milliseconds after which
     *                                 no more retries should be attempted.
     */
    public function __construct(int $timeoutMilliseconds)
    {
        $this->timeoutMilliseconds = $timeoutMilliseconds;
    }

    /**
     * Determines whether a retry should be performed based on the exception
     * and the context of the operation.
     *
     * @param Exception $e The exception that caused the operation to fail.
     * @param RetryContext $context The context of the operation, containing
     *                              information such as the number of attempts
     *                              so far and the last exception.
     * @return bool Returns true if a retry should be performed, false
     *              otherwise.
     */
    public function shouldRetry(Exception $e, RetryContext $context): bool
    {
        // Calculate the elapsed time since the start of the operation in milliseconds.
        $elapsedTime =  intval((microtime(true) - $context->getStartTime()) * 1000);

        // If the elapsed time is less than the configured timeout, the operation should be retried.
        return $elapsedTime < $this->timeoutMilliseconds;
    }
}
