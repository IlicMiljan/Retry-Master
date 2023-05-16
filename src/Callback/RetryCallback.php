<?php

namespace IlicMiljan\RetryMaster\Callback;

use IlicMiljan\RetryMaster\Context\RetryContext;

/**
 * Interface RetryCallback
 *
 * RetryCallback is an interface that should be implemented by classes that
 * define an operation to be retried. The operation is defined in the
 * doWithRetry method, which accepts a RetryContext parameter and returns a
 * result.
 *
 * The RetryContext provides information about the retry operation, such as the
 * number of attempts so far and the last exception that caused the operation
 * to fail.
 *
 * The result of the doWithRetry method can be any type (as indicated by the
 * return type hint of 'mixed'). This allows the RetryCallback to be used for a
 * wide variety of operations.
 *
 * @package IlicMiljan\RetryMaster\Callback
 */
interface RetryCallback
{
    /**
     * Executes the operation to be retried.
     *
     * @param RetryContext $context The context of the retry operation, containing
     * information such as the number of attempts so far and the last exception.
     * @return mixed The result of the operation.
     */
    public function doWithRetry(RetryContext $context);
}
