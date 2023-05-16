<?php

namespace IlicMiljan\RetryMaster\Policy\Retry;

use Exception;
use IlicMiljan\RetryMaster\Context\RetryContext;

/**
 * ## AlwaysRetryPolicy
 *
 * AlwaysRetryPolicy is an implementation of the RetryPolicy interface
 * that always allows a retry, irrespective of the type of exception or
 * the number of attempts so far.
 *
 * This class can be used in scenarios where you want to keep retrying
 * indefinitely until the operation succeeds. However, it should be used
 * with caution, as it can potentially lead to an infinite loop if the
 * operation always fails. It's often a good idea to combine this policy
 * with a BackoffPolicy that increases the delay between retries, to avoid
 * overloading the system.
 *
 * @package IlicMiljan\RetryMaster\Policy\Retry
 */
class AlwaysRetryPolicy implements RetryPolicy
{
    /**
     * Determines whether a retry should be performed based on the exception
     * and the context of the operation.
     *
     * @param Exception $e The exception that caused the operation to fail.
     * @param RetryContext $context The context of the operation, containing
     *                              information such as the number of attempts
     *                              so far and the last exception.
     * @return bool Always returns true for this policy.
     */
    public function shouldRetry(Exception $e, RetryContext $context): bool
    {
        return true;
    }
}
