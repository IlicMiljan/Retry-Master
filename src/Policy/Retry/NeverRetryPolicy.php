<?php

namespace IlicMiljan\RetryMaster\Policy\Retry;

use Exception;
use IlicMiljan\RetryMaster\Context\RetryContext;

/**
 * ## NeverRetryPolicy
 *
 * NeverRetryPolicy is an implementation of the RetryPolicy interface that
 * disallows any retry attempts, regardless of the operation or its result.
 *
 * This policy is useful in scenarios where you do not want any retries to be
 * performed for a certain operation, irrespective of whether it fails or not.
 * Using this policy, all operations will be attempted exactly once.
 *
 * @package IlicMiljan\RetryMaster\Policy\Retry
 */
class NeverRetryPolicy implements RetryPolicy
{
    /**
     * Determines whether a retry should be performed based on the exception
     * and the context of the operation.
     *
     * @param Exception $e The exception that caused the operation to fail.
     * @param RetryContext $context The context of the operation, containing
     *                              information such as the number of attempts
     *                              so far and the last exception.
     * @return bool Returns false, indicating that the operation should not be
     *              retried.
     */
    public function shouldRetry(Exception $e, RetryContext $context): bool
    {
        return false;
    }
}
