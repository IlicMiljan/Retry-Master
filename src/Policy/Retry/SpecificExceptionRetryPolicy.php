<?php

namespace IlicMiljan\RetryMaster\Policy\Retry;

use Exception;
use IlicMiljan\RetryMaster\Context\RetryContext;

/**
 * ## SpecificExceptionRetryPolicy
 *
 * SpecificExceptionRetryPolicy is an implementation of RetryPolicy that decides
 * to retry a failed operation based on the type of exception that occurred.
 *
 * It is initialized with a specific exception class, and the shouldRetry method
 * will return true if the exception that caused the failure is an instance of
 * the configured class.
 *
 * @package IlicMiljan\RetryMaster\Policy\Retry
 */
class SpecificExceptionRetryPolicy implements RetryPolicy
{
    private string $exceptionClass;

    /**
     * @param string $exceptionClass The class of exception for which a retry
     *                               should be attempted.
     */
    public function __construct(string $exceptionClass)
    {
        $this->exceptionClass = $exceptionClass;
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
        return $e instanceof $this->exceptionClass;
    }
}
