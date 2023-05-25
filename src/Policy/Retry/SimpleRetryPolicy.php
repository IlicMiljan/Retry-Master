<?php

namespace IlicMiljan\RetryMaster\Policy\Retry;

use Exception;
use IlicMiljan\RetryMaster\Context\RetryContext;

/**
 * ## SimpleRetryPolicy
 *
 * SimpleRetryPolicy is an implementation of RetryPolicy that retries a failed
 * operation a fixed number of times, and for a specific set of exceptions.
 *
 * It is configurable with a maxAttempts property and a retryableExceptions list.
 * The shouldRetry method will return true if the exception is either in the
 * list of retryable exceptions or if the list is empty, and the maximum number
 * of attempts has not been reached.
 *
 * @package IlicMiljan\RetryMaster\Policy\Retry
 */
class SimpleRetryPolicy implements RetryPolicy
{
    private int $maxAttempts;
    /**
     * @var string[]
     */
    private array $retryableExceptions;

    /**
     * @param int $maxAttempts Maximum number of attempts before giving up.
     * @param string[] $retryableExceptions List of exceptions for which a
     *                                         retry should be attempted.
     */
    public function __construct(int $maxAttempts = 3, array $retryableExceptions = [])
    {
        $this->maxAttempts = $maxAttempts;
        $this->retryableExceptions = $retryableExceptions;
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
        if ($context->getRetryCount() > $this->maxAttempts) {
            return false;
        }

        // If no specific exceptions are defined, retry on all exceptions.
        if (empty($this->retryableExceptions)) {
            return true;
        }

        foreach ($this->retryableExceptions as $retryableException) {
            // Retry if the exception is an instance of a retryable exception.
            if ($e instanceof $retryableException) {
                return true;
            }
        }

        return false;
    }
}
