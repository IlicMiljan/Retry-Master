<?php

namespace IlicMiljan\RetryMaster\Policy\Retry;

use Exception;
use IlicMiljan\RetryMaster\Context\RetryContext;

/**
 * ## RetryPolicy
 *
 * The RetryPolicy interface provides a contract for retry policy
 * implementations in the RetryMaster framework.
 *
 * The shouldRetry method must be implemented by classes that use this interface.
 * This method is called whenever an operation has failed with an exception,
 * and it should determine whether a retry should be performed based on the
 * exception and the context of the operation.
 *
 * @package IlicMiljan\RetryMaster\Policy\Retry
 */
interface RetryPolicy
{
    /**
     * Determines whether a retry should be performed based on the exception and
     * the context of the operation.
     *
     * @param Exception $e The exception that caused the operation to fail.
     * @param RetryContext $context The context of the operation, containing
     *                              information such as the number of attempts
     *                              so far and the last exception.
     * @return bool Returns true if a retry should be performed, false
     *              otherwise.
     */
    public function shouldRetry(Exception $e, RetryContext $context): bool;
}
