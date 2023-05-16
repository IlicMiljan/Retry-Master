<?php

namespace IlicMiljan\RetryMaster\Policy\Retry;

use Exception;
use IlicMiljan\RetryMaster\Context\RetryContext;

/**
 * ## NonRepeatingExceptionRetryPolicy
 *
 * NonRepeatingExceptionRetryPolicy is an implementation of the RetryPolicy interface
 * that allows a retry only if the exception type thrown by the last failed attempt
 * is different from the current exception type.
 *
 * This policy is beneficial in scenarios where an operation is expected to fail
 * repeatedly with the same type of exception, and retrying would not change the outcome.
 *
 * @package IlicMiljan\RetryMaster\Policy\Retry
 */
class NonRepeatingExceptionRetryPolicy implements RetryPolicy
{
    /**
     * Determines whether a retry should be performed based on the exception
     * and the context of the operation.
     *
     * @param Exception $e The exception that caused the operation to fail.
     * @param RetryContext $context The context of the operation, containing
     * information such as the number of attempts so far and the last exception.
     * @return bool Returns true if there was no previous exception or the previous
     * exception is of a different type. Returns false if the last exception
     * is of the same type as the current exception.
     */
    public function shouldRetry(Exception $e, RetryContext $context): bool
    {
        $lastException = $context->getLastException();

        // If there was no previous exception or the previous exception is of a different type, retry.
        if ($lastException === null || get_class($lastException) !== get_class($e)) {
            return true;
        }

        // If the last exception is of the same type as the current exception, don't retry.
        return false;
    }
}
